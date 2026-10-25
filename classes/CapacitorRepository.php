<?php
class CapacitorRepository
{
    public function __construct(private PDO $pdo) {}

    /** @return array<int, */
    public function getTypes(): array
    {
        $rows = $this->pdo->query('SELECT * FROM capacitor_types ORDER BY id')->fetchAll();

        $types = [];
        foreach ($rows as $row) {
            $types[(int) $row['id']] = new CapacitorType((int) $row['id'], $row['name']);
        }
        return $types;
    }

    public function findTypeById(int $id): ?CapacitorType
    {
        $stmt = $this->pdo->prepare('SELECT * FROM capacitor_types WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? new CapacitorType((int) $row['id'], $row['name']) : null;
    }

    public function typeNameExists(string $name): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM capacitor_types WHERE name = :name LIMIT 1');
        $stmt->execute(['name' => $name]);
        return $stmt->fetchColumn() !== false;
    }

    public function createType(string $name): CapacitorType
    {
        $stmt = $this->pdo->prepare('INSERT INTO capacitor_types (name) VALUES (:name)');
        $stmt->execute(['name' => $name]);
        return new CapacitorType((int) $this->pdo->lastInsertId(), $name);
    }

    /** @return array<int, */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM capacitors ORDER BY id');

        $capacitors = [];
        foreach ($stmt->fetchAll() as $row) {
            $capacitors[(int) $row['id']] = $this->hydrateCapacitor($row);
        }
        return $capacitors;
    }

    public function findById(int $id): ?Capacitor
    {
        $stmt = $this->pdo->prepare('SELECT * FROM capacitors WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrateCapacitor($row) : null;
    }

    public function create(
        string $designation, CapacitorType $type, float $capacitance, float $voltage
    ): Capacitor {
        $stmt = $this->pdo->prepare(
            'INSERT INTO capacitors (designation, type_id, capacitance_uf, voltage_v)
             VALUES (:designation, :type_id, :capacitance, :voltage)'
        );
        $stmt->execute([
            'designation' => $designation,
            'type_id'     => $type->getId(),
            'capacitance' => $capacitance,
            'voltage'     => $voltage,
        ]);
        return new Capacitor((int) $this->pdo->lastInsertId(), $designation, $type, $capacitance, $voltage);
    }

    public function attachPhoto(int $capacitorId, string $photoFilename): bool
    {
        if ($this->findById($capacitorId) === null) {
            return false;
        }
        $stmt = $this->pdo->prepare('UPDATE capacitors SET photo = :photo WHERE id = :id');
        return $stmt->execute(['photo' => $photoFilename, 'id' => $capacitorId]);
    }

    public function addParameterToCapacitor(int $capacitorId, string $name, string $value): bool
    {
        if ($this->findById($capacitorId) === null) {
            return false;
        }
        $stmt = $this->pdo->prepare(
            'INSERT INTO parameters (capacitor_id, name, value) VALUES (:capacitor_id, :name, :value)'
        );
        return $stmt->execute(['capacitor_id' => $capacitorId, 'name' => $name, 'value' => $value]);
    }

    public function update(int $id, array $data): bool
    {
        if ($this->findById($id) === null) {
            return false;
        }
        $columns = [];
        $params  = ['id' => $id];
        if (isset($data['designation'])) { $columns[] = 'designation = :designation'; $params['designation'] = $data['designation']; }
        if (isset($data['capacitance'])) { $columns[] = 'capacitance_uf = :capacitance'; $params['capacitance'] = (float) $data['capacitance']; }
        if (isset($data['voltage']))     { $columns[] = 'voltage_v = :voltage';          $params['voltage']     = (float) $data['voltage']; }
        if (isset($data['type']))        { $columns[] = 'type_id = :type_id';            $params['type_id']     = $data['type']->getId(); }

        if ($columns === []) {
            return true;
        }
        $stmt = $this->pdo->prepare('UPDATE capacitors SET ' . implode(', ', $columns) . ' WHERE id = :id');
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM capacitors WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function save(): void {}

    private function hydrateCapacitor(array $row): Capacitor
    {
        $capacitor = new Capacitor(
            (int) $row['id'], $row['designation'], $this->findTypeById((int) $row['type_id']),
            (float) $row['capacitance_uf'], (float) $row['voltage_v']
        );
        if ($row['photo'] !== null && $row['photo'] !== '') {
            $capacitor->setPhoto($row['photo']);
        }

        $stmt = $this->pdo->prepare('SELECT * FROM parameters WHERE capacitor_id = :id ORDER BY id');
        $stmt->execute(['id' => $row['id']]);
        foreach ($stmt->fetchAll() as $paramRow) {
            $capacitor->addParameter(new Parameter($paramRow['name'], $paramRow['value']));
        }
        return $capacitor;
    }
}
