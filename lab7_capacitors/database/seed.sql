USE capacitors_db;

INSERT INTO capacitor_types (id, name) VALUES
    (1, 'Керамічний'),
    (2, 'Електролітичний'),
    (3, 'Плівковий');

INSERT INTO capacitors (id, designation, type_id, capacitance_uf, voltage_v, photo) VALUES
    (1, 'К10-17', 1, 0.1,   50, 'capacitor_seed1.png'),
    (2, 'К50-35', 2, 1000,  16, 'capacitor_seed2.png'),
    (3, 'К73-17', 3, 0.33,  63, 'capacitor_seed3.png'),
    (4, 'К52-1',  2, 47,    10, 'capacitor_seed4.png');

INSERT INTO parameters (capacitor_id, name, value) VALUES
    (1, 'Маркування', '104'),
    (1, 'Допуск', '±10%'),
    (1, 'Діелектрик', 'X7R'),
    (2, 'Маркування', '1000мкФ 16В'),
    (2, 'Допуск', '±20%'),
    (2, 'Полярність', 'Так'),
    (3, 'Маркування', '334'),
    (3, 'Допуск', '±5%'),
    (3, 'Матеріал плівки', 'Поліестер'),
    (4, 'Маркування', '47мкФ 10В'),
    (4, 'Полярність', 'Так'),
    (4, 'Корпус', 'Case B');
