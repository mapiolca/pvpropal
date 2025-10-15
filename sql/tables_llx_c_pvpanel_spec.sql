-- Create dictionary for PV panel specifications. (EN)
-- Création du dictionnaire des spécifications de panneaux PV. (FR)
CREATE TABLE IF NOT EXISTS llx_c_pvpanel_spec (
	rowid integer AUTO_INCREMENT PRIMARY KEY,
	entity integer NOT NULL DEFAULT 1,
	code varchar(128) NOT NULL,
	label varchar(255) NOT NULL,
	unit varchar(64) NOT NULL,
	active integer NOT NULL DEFAULT 1,
	tms timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=innodb;

ALTER TABLE llx_c_pvpanel_spec ADD UNIQUE INDEX uk_c_pvpanel_spec_code_entity (entity, code);
