CREATE DATABASE IF NOT EXISTS rest_control CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rest_control;
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS chat_messages;
DROP TABLE IF EXISTS announcements;
DROP TABLE IF EXISTS chat_members;
DROP TABLE IF EXISTS chat_conversations;
DROP TABLE IF EXISTS overtime_alerts;
DROP TABLE IF EXISTS rest_requests;
DROP TABLE IF EXISTS supervisor_permissions;
DROP TABLE IF EXISTS experts;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE users (
 id INT UNSIGNED NOT NULL AUTO_INCREMENT,
 username VARCHAR(100) NOT NULL,
 password VARCHAR(255) NOT NULL,
 role ENUM('admin','supervisor') NOT NULL,
 display_name VARCHAR(150) NOT NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (id), UNIQUE KEY uq_users_username(username), KEY idx_users_role(role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE experts (
 id INT UNSIGNED NOT NULL AUTO_INCREMENT,
 username VARCHAR(100) NOT NULL,
 password VARCHAR(255) NOT NULL,
 supervisor_username VARCHAR(100) NOT NULL,
 display_name VARCHAR(150) NOT NULL,
 daily_rest_limit SMALLINT UNSIGNED NOT NULL DEFAULT 3,
 rest_login_count INT UNSIGNED NOT NULL DEFAULT 0,
 last_rest_login_date DATE NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (id), UNIQUE KEY uq_experts_username(username), KEY idx_experts_supervisor(supervisor_username),
 CONSTRAINT fk_experts_supervisor FOREIGN KEY(supervisor_username) REFERENCES users(username) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE supervisor_permissions (
 id INT UNSIGNED NOT NULL AUTO_INCREMENT,
 supervisor_username VARCHAR(100) NOT NULL,
 can_approve TINYINT(1) NOT NULL DEFAULT 1,
 can_reject TINYINT(1) NOT NULL DEFAULT 1,
 can_force_end TINYINT(1) NOT NULL DEFAULT 1,
 can_manual_rest TINYINT(1) NOT NULL DEFAULT 1,
 can_chat TINYINT(1) NOT NULL DEFAULT 1,
 can_view_other_teams TINYINT(1) NOT NULL DEFAULT 1,
 updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY(id), UNIQUE KEY uq_perm_sup(supervisor_username),
 CONSTRAINT fk_perm_sup FOREIGN KEY(supervisor_username) REFERENCES users(username) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE rest_requests (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
 expert_username VARCHAR(100) NOT NULL,
 supervisor_username VARCHAR(100) NOT NULL,
 status ENUM('pending','approved','rejected','completed','overtime','cancelled') NOT NULL DEFAULT 'pending',
 request_type ENUM('normal','emergency') NOT NULL DEFAULT 'normal',
 reason VARCHAR(500) NULL,
 requested_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 approved_at DATETIME NULL,
 completed_at DATETIME NULL,
 duration_minutes SMALLINT UNSIGNED NULL,
 overtime_seconds INT UNSIGNED NOT NULL DEFAULT 0,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY(id), KEY idx_rr_sup_status_id(supervisor_username,status,id), KEY idx_rr_expert_status_id(expert_username,status,id), KEY idx_rr_created(created_at), KEY idx_rr_queue(status,requested_at,id),
 CONSTRAINT fk_rr_expert FOREIGN KEY(expert_username) REFERENCES experts(username) ON UPDATE CASCADE ON DELETE RESTRICT,
 CONSTRAINT fk_rr_supervisor FOREIGN KEY(supervisor_username) REFERENCES users(username) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE overtime_alerts (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
 expert_username VARCHAR(100) NOT NULL,
 supervisor_username VARCHAR(100) NOT NULL,
 rest_request_id BIGINT UNSIGNED NOT NULL,
 overtime_seconds INT UNSIGNED NOT NULL DEFAULT 0,
 alert_date DATE NOT NULL,
 message VARCHAR(500) NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY(id), UNIQUE KEY uq_overtime_request(rest_request_id), KEY idx_oa_sup_created(supervisor_username,created_at),
 CONSTRAINT fk_oa_request FOREIGN KEY(rest_request_id) REFERENCES rest_requests(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE announcements (id INT AUTO_INCREMENT PRIMARY KEY,message TEXT NOT NULL,created_by VARCHAR(100) NOT NULL,created_by_role ENUM('admin','supervisor') NOT NULL,created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,INDEX idx_ann_created_at(created_at)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE chat_conversations (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
 type ENUM('direct','group') NOT NULL,
 title VARCHAR(150) NULL,
 created_by VARCHAR(100) NOT NULL,
 created_by_role ENUM('admin','supervisor','expert') NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY(id), KEY idx_chat_created(created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE chat_members (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
 conversation_id BIGINT UNSIGNED NOT NULL,
 username VARCHAR(100) NOT NULL,
 role ENUM('admin','supervisor','expert') NOT NULL,
 display_name VARCHAR(150) NOT NULL,
 joined_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY(id), UNIQUE KEY uq_chat_member(conversation_id,username,role), KEY idx_chat_user(username),
 CONSTRAINT fk_chat_member_conv FOREIGN KEY(conversation_id) REFERENCES chat_conversations(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE chat_messages (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
 conversation_id BIGINT UNSIGNED NOT NULL,
 sender_username VARCHAR(100) NOT NULL,
 sender_role ENUM('admin','supervisor','expert') NOT NULL,
 sender_name VARCHAR(150) NOT NULL,
 body TEXT NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY(id), KEY idx_chat_msg_conv_id(conversation_id,id),
 CONSTRAINT fk_chat_msg_conv FOREIGN KEY(conversation_id) REFERENCES chat_conversations(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
