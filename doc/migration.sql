ALTER TABLE `enfant` DROP `uuid`;
RENAME TABLE `hotton`.`enfant_tuteur` TO `hotton`.`relation`;
ALTER TABLE `tuteur` CHANGE `adresse` `rue` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
RENAME TABLE `hotton`.`users` TO `hotton`.`user`;
ALTER TABLE `plaine` CHANGE `intitule` `nom` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `tuteur` CHANGE `conjoint` `relation_conjoint` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'belle-mere, pere, mere';
ALTER TABLE `sante_question` CHANGE `intitule` `nom` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `note` CHANGE `contenu` `remarque` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `animateur` CHANGE `adresse` `rue` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `plaine` CHANGE `premat` `prematernelle` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `note` CHANGE `cloture` `archive` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `enfant` CHANGE `image_name` `photo_name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
UPDATE `sante_question` SET complement = 0 WHERE complement IS NULL
ALTER TABLE `user` DROP `roles`;
UPDATE enfant SET created_at = NOW(), updated_at = NOW();
UPDATE animateur SET created_at = NOW(), updated_at = NOW();
UPDATE tuteur SET created_at = NOW(), updated_at = NOW();
UPDATE jour SET created_at = NOW(), updated_at = NOW();
UPDATE message SET created_at = NOW(), updated_at = NOW();
UPDATE plaine SET created_at = NOW(), updated_at = NOW();
UPDATE presence SET created_at = NOW(), updated_at = NOW();
UPDATE sante_fiche SET created_at = NOW(), updated_at = NOW();
UPDATE presence SET created_at = NOW(), updated_at = NOW();

//simon.habran@gmail.com => tuteur
