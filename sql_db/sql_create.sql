DROP VIEW IF EXISTS view_power_logs, view_user_data, view_temperature_logs, view_purifier_logs;

DROP TABLE IF EXISTS power_data_readings, temperature_data_readings, purifier_data_readings,
    tasmota_data_readings, power_day_stats, data_logs, devices, users, dev_data_type;

CREATE TABLE users(
    user_id TINYINT UNSIGNED AUTO_INCREMENT,
    user_name CHAR(100) UNIQUE,
    email CHAR(30) UNIQUE,
    user_password_hash CHAR(60) NOT NULL,
    api_key CHAR(64) NOT NULL,
    api_hash CHAR(40) NOT NULL,
    PRIMARY KEY (user_id)
)ENGINE=InnoDB;

CREATE TABLE dev_data_type(
    dt_id TINYINT UNSIGNED AUTO_INCREMENT NOT NULL,
    dt_name CHAR(50) UNIQUE,
    dt_description CHAR(50),
    PRIMARY KEY (dt_id)
)ENGINE=InnoDB;

CREATE TABLE devices(
    device_id TINYINT UNSIGNED AUTO_INCREMENT,
    user_id TINYINT UNSIGNED,
    dev_name CHAR(100) NOT NULL,
    dt_id TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (device_id),
    UNIQUE (user_id, dt_id, dev_name),
    FOREIGN KEY (user_id) REFERENCES users (user_id),
    FOREIGN KEY (dt_id) REFERENCES dev_data_type(dt_id)
)ENGINE=InnoDB;

CREATE TABLE data_logs (
    data_id INT UNSIGNED AUTO_INCREMENT,
    user_id TINYINT UNSIGNED NOT NULL,
    readout_time TIMESTAMP NOT NULL,
    dt_id TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (data_id, readout_time),
    FOREIGN KEY (user_id) REFERENCES users (user_id),
    FOREIGN KEY (dt_id) REFERENCES devices (dt_id),
    INDEX (readout_time),
    UNIQUE (dt_id, user_id, readout_time)
)ENGINE=InnoDB ROW_FORMAT=COMPRESSED;

CREATE TABLE power_data_readings(
    data_id INT UNSIGNED NOT NULL,
    device_id TINYINT UNSIGNED NOT NULL,
    ac_power DECIMAL(4,0) UNSIGNED,
    dc_voltage DECIMAL(5,1) UNSIGNED,
    dc_current DECIMAL(4,2) UNSIGNED,
    cpu_temperature DECIMAL(4,1),
    radiator_temperature DECIMAL(4,1),
    grid_voltage DECIMAL(5,1) UNSIGNED,
    grid_current DECIMAL(4,2) UNSIGNED,
    grid_frequency DECIMAL(3,1) UNSIGNED,
    PRIMARY KEY (device_id, data_id),
    FOREIGN KEY (data_id) REFERENCES data_logs (data_id),
    FOREIGN KEY (device_id) REFERENCES devices (device_id)
)ENGINE=InnoDB ROW_FORMAT=COMPRESSED;

CREATE TABLE temperature_data_readings(
    data_id INT UNSIGNED NOT NULL,
    device_id TINYINT UNSIGNED NOT NULL,
    temperature DECIMAL(4,1),
    PRIMARY KEY (device_id, data_id),
    FOREIGN KEY (data_id) REFERENCES data_logs (data_id),
    FOREIGN KEY (device_id) REFERENCES devices (device_id)
)ENGINE=InnoDB ROW_FORMAT=COMPRESSED;

CREATE TABLE purifier_data_readings(
    data_id INT UNSIGNED NOT NULL,
    device_id TINYINT UNSIGNED NOT NULL,
    aqi SMALLINT UNSIGNED,
    humidity TINYINT UNSIGNED,
    temperature DECIMAL(4,1),
    fan_rpm SMALLINT UNSIGNED,
    PRIMARY KEY (device_id, data_id),
    FOREIGN KEY (data_id) REFERENCES data_logs (data_id),
    FOREIGN KEY (device_id) REFERENCES devices (device_id)
)ENGINE=InnoDB ROW_FORMAT=COMPRESSED;

CREATE TABLE tasmota_data_readings(
    data_id INT UNSIGNED NOT NULL,
    device_id TINYINT UNSIGNED NOT NULL,
    ac_power DECIMAL(4,0) UNSIGNED,
    ac_voltage DECIMAL(4,1),
    ac_current DECIMAL(4,2),
    PRIMARY KEY (device_id, data_id),
    FOREIGN KEY (data_id) REFERENCES data_logs (data_id),
    FOREIGN KEY (device_id) REFERENCES devices (device_id)
)ENGINE=InnoDB ROW_FORMAT=COMPRESSED;

CREATE TABLE power_day_stats(
    user_id TINYINT UNSIGNED NOT NULL,
    device_id TINYINT UNSIGNED NOT NULL,
    day_production DATE,
    kwh DECIMAL(5,2),
    FOREIGN KEY (user_id) REFERENCES users (user_id),
    FOREIGN KEY (device_id) REFERENCES devices (device_id),
    UNIQUE (user_id, device_id, day_production)
)ENGINE=InnoDB;

/*---------------------------- INIT --------------------------------------------------*/
INSERT INTO `dev_data_type` (`dt_name`, `dt_description`) VALUES
    ('santerno_readouts', 'Santerno PV Data'),
    ('ds18b20_readouts', 'DS18B20 Temperature Data'),
    ('purifier_readouts', 'Xiaomi AirPurifier Data'),
    ('tasmota_readouts', 'Tasmota Plug Data');

/* Warning: It is sample auth data below. Need to be changed. */
INSERT INTO `users` (`user_name`, `user_password_hash`, `api_key`, `api_hash`) VALUES
('PI_Zero', '$2b$12$nNCVIofFKjg4F0acMTvjx.NksycLdqxLwbj/vckAidZL0B2pkAyfK', 'dad6f9894a328abf65466919a8dba2cd280758e64936882daa2bc22a9f911234', '831fe1b07d15fff8c9d6487e2ba1d77f82dd68de');


INSERT INTO `devices` (`user_id`, `dt_id`, `dev_name`)
    VALUES (LAST_INSERT_ID(), (SELECT dt_id FROM dev_data_type WHERE dt_name = 'ds18b20_readouts'), 'Room'),
    (LAST_INSERT_ID(), (SELECT dt_id FROM dev_data_type WHERE dt_name = 'purifier_readouts'), 'XiaomiAirPurifier2'),
    (LAST_INSERT_ID(), (SELECT dt_id FROM dev_data_type WHERE dt_name = 'tasmota_readouts'), 'LanbergPlug'),
    (LAST_INSERT_ID(), (SELECT dt_id FROM dev_data_type WHERE dt_name = 'tasmota_readouts'), 'BlitzwolfPlug');

CREATE OR REPLACE VIEW view_power_logs AS
SELECT D.user_id, L.readout_time, D.dev_name, D.device_id, P.ac_power, P.dc_current, P.dc_voltage, P.cpu_temperature, P.radiator_temperature FROM power_data_readings P
    LEFT JOIN data_logs L ON (L.data_id = P.data_id)
    LEFT JOIN devices D ON (D.device_id = P.device_id);
    
CREATE OR REPLACE VIEW view_temperature_logs AS
SELECT L.data_id, D.user_id, L.readout_time, D.dev_name, T.temperature FROM temperature_data_readings T
    LEFT JOIN data_logs L ON (L.data_id = T.data_id)
    LEFT JOIN devices D ON (D.device_id = T.device_id);

CREATE OR REPLACE VIEW view_purifier_logs AS
SELECT D.user_id, L.readout_time, D.dev_name, P.aqi, P.humidity, P.temperature, P.fan_rpm  FROM purifier_data_readings P
    LEFT JOIN data_logs L ON (L.data_id = P.data_id)
    LEFT JOIN devices D ON (D.device_id = P.device_id);
    
CREATE OR REPLACE VIEW view_tasmota_logs AS
SELECT D.user_id, L.readout_time, D.dev_name, P.ac_voltage, P.ac_power, P.ac_current FROM tasmota_data_readings P
    LEFT JOIN data_logs L ON (L.data_id = P.data_id)
    LEFT JOIN devices D ON (D.device_id = P.device_id);
    
CREATE VIEW view_user_data AS
SELECT D.dev_name, D.device_id, U.user_name, U.user_id FROM users U 
    LEFT JOIN devices D ON (D.user_id = U.user_id);
