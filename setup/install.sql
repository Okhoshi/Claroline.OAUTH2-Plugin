CREATE TABLE IF NOT EXISTS `__CL_MAIN__oauth_clients` (
  `client_id`     VARCHAR(80)   NOT NULL,
  `client_secret` VARCHAR(80)   NOT NULL,
  `redirect_uri`  VARCHAR(2000) NOT NULL,
  `grant_types`   VARCHAR(80),
  `scope`         INT(11),
  `user_id`       INT(11),
  CONSTRAINT `client_id_pk` PRIMARY KEY (`client_id`)
)
  ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_MAIN__oauth_access_tokens` (
  `access_token` VARCHAR(40) NOT NULL,
  `client_id`    VARCHAR(80) NOT NULL,
  `user_id`      INT(11),
  `expires`      TIMESTAMP   NOT NULL,
  `scope`        INT(11),
  CONSTRAINT `access_token_pk` PRIMARY KEY (`access_token`)
)
  ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_MAIN__oauth_authorization_codes` (
  `authorization_code` VARCHAR(40) NOT NULL,
  `client_id`          VARCHAR(80) NOT NULL,
  `user_id`            INT(11),
  `redirect_uri`       VARCHAR(2000),
  `expires`            TIMESTAMP   NOT NULL,
  `scope`              INT(11),
  CONSTRAINT `auth_code_pk` PRIMARY KEY (`authorization_code`)
)
  ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_MAIN__oauth_refresh_tokens` (
  `refresh_token` VARCHAR(40) NOT NULL,
  `client_id`     VARCHAR(80) NOT NULL,
  `user_id`       INT(11),
  `expires`       TIMESTAMP   NOT NULL,
  `scope`         INT(11),
  CONSTRAINT `refresh_token_pk` PRIMARY KEY (`refresh_token`)
)
  ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_MAIN__oauth_scopes` (
  `id`         INT(11) NOT NULL auto_increment,
  `scope`      TEXT,
  `is_default` BOOLEAN,
  CONSTRAINT `scope_pk` PRIMARY KEY (`id`)
)
  ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_MAIN__oauth_jwt` (
  `client_id`  VARCHAR(80) NOT NULL,
  `subject`    VARCHAR(80),
  `public_key` VARCHAR(2000),
  CONSTRAINT `client_id_pk` PRIMARY KEY (`client_id`)
)
  ENGINE = MyISAM;
