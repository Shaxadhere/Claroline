<?php

namespace Claroline\CoreBundle\Migrations\pdo_sqlite;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated migration based on mapping information: modify it with caution
 *
 * Generation date: 2013/10/03 02:24:29
 */
class Version20131003142428 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE claro_log_widget_config (
                id INTEGER NOT NULL, 
                amount INTEGER NOT NULL, 
                restrictions CLOB DEFAULT NULL, 
                widgetInstance_id INTEGER DEFAULT NULL, 
                PRIMARY KEY(id)
            )
        ");
        $this->addSql("
            CREATE INDEX IDX_C16334B2AB7B5A55 ON claro_log_widget_config (widgetInstance_id)
        ");
        $this->addSql("
            CREATE TABLE claro_widget_instance (
                id INTEGER NOT NULL, 
                workspace_id INTEGER DEFAULT NULL, 
                user_id INTEGER DEFAULT NULL, 
                widget_id INTEGER NOT NULL, 
                is_admin BOOLEAN NOT NULL, 
                is_desktop BOOLEAN NOT NULL, 
                name VARCHAR(255) NOT NULL, 
                PRIMARY KEY(id)
            )
        ");
        $this->addSql("
            CREATE INDEX IDX_5F89A38582D40A1F ON claro_widget_instance (workspace_id)
        ");
        $this->addSql("
            CREATE INDEX IDX_5F89A385A76ED395 ON claro_widget_instance (user_id)
        ");
        $this->addSql("
            CREATE INDEX IDX_5F89A385FBE885E2 ON claro_widget_instance (widget_id)
        ");
        $this->addSql("
            CREATE TABLE claro_simple_text_widget_config (
                id INTEGER NOT NULL, 
                content CLOB NOT NULL, 
                widgetInstance_id INTEGER DEFAULT NULL, 
                PRIMARY KEY(id)
            )
        ");
        $this->addSql("
            CREATE INDEX IDX_C389EBCCAB7B5A55 ON claro_simple_text_widget_config (widgetInstance_id)
        ");
        $this->addSql("
            ALTER TABLE claro_widget 
            ADD COLUMN is_displayable_in_workspace BOOLEAN NOT NULL
        ");
        $this->addSql("
            ALTER TABLE claro_widget 
            ADD COLUMN is_displayable_in_desktop BOOLEAN NOT NULL
        ");
        $this->addSql("
            DROP INDEX IDX_D48CC23EFBE885E2
        ");
        $this->addSql("
            DROP INDEX IDX_D48CC23E7D08FA9E
        ");
        $this->addSql("
            DROP INDEX IDX_D48CC23EA76ED395
        ");
        $this->addSql("
            DROP INDEX IDX_D48CC23E82D40A1F
        ");
        $this->addSql("
            CREATE TEMPORARY TABLE __temp__claro_widget_home_tab_config AS 
            SELECT id, 
            workspace_id, 
            home_tab_id, 
            user_id, 
            widget_order, 
            type, 
            is_visible, 
            is_locked 
            FROM claro_widget_home_tab_config
        ");
        $this->addSql("
            DROP TABLE claro_widget_home_tab_config
        ");
        $this->addSql("
            CREATE TABLE claro_widget_home_tab_config (
                id INTEGER NOT NULL, 
                workspace_id INTEGER DEFAULT NULL, 
                home_tab_id INTEGER NOT NULL, 
                user_id INTEGER DEFAULT NULL, 
                widget_instance_id INTEGER DEFAULT NULL, 
                widget_order VARCHAR(255) NOT NULL, 
                type VARCHAR(255) NOT NULL, 
                is_visible BOOLEAN NOT NULL, 
                is_locked BOOLEAN NOT NULL, 
                PRIMARY KEY(id), 
                CONSTRAINT FK_D48CC23E82D40A1F FOREIGN KEY (workspace_id) 
                REFERENCES claro_workspace (id) 
                ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, 
                CONSTRAINT FK_D48CC23E7D08FA9E FOREIGN KEY (home_tab_id) 
                REFERENCES claro_home_tab (id) 
                ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, 
                CONSTRAINT FK_D48CC23EA76ED395 FOREIGN KEY (user_id) 
                REFERENCES claro_user (id) 
                ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, 
                CONSTRAINT FK_D48CC23E44BF891 FOREIGN KEY (widget_instance_id) 
                REFERENCES claro_widget_instance (id) 
                ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
            )
        ");
        $this->addSql("
            INSERT INTO claro_widget_home_tab_config (
                id, workspace_id, home_tab_id, user_id, 
                widget_order, type, is_visible, is_locked
            ) 
            SELECT id, 
            workspace_id, 
            home_tab_id, 
            user_id, 
            widget_order, 
            type, 
            is_visible, 
            is_locked 
            FROM __temp__claro_widget_home_tab_config
        ");
        $this->addSql("
            DROP TABLE __temp__claro_widget_home_tab_config
        ");
        $this->addSql("
            CREATE INDEX IDX_D48CC23E7D08FA9E ON claro_widget_home_tab_config (home_tab_id)
        ");
        $this->addSql("
            CREATE INDEX IDX_D48CC23EA76ED395 ON claro_widget_home_tab_config (user_id)
        ");
        $this->addSql("
            CREATE INDEX IDX_D48CC23E82D40A1F ON claro_widget_home_tab_config (workspace_id)
        ");
        $this->addSql("
            CREATE INDEX IDX_D48CC23E44BF891 ON claro_widget_home_tab_config (widget_instance_id)
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            DROP TABLE claro_log_widget_config
        ");
        $this->addSql("
            DROP TABLE claro_widget_instance
        ");
        $this->addSql("
            DROP TABLE claro_simple_text_widget_config
        ");
        $this->addSql("
            DROP INDEX UNIQ_76CA6C4F5E237E06
        ");
        $this->addSql("
            DROP INDEX IDX_76CA6C4FEC942BCF
        ");
        $this->addSql("
            CREATE TEMPORARY TABLE __temp__claro_widget AS 
            SELECT id, 
            plugin_id, 
            name, 
            is_configurable, 
            icon, 
            is_exportable 
            FROM claro_widget
        ");
        $this->addSql("
            DROP TABLE claro_widget
        ");
        $this->addSql("
            CREATE TABLE claro_widget (
                id INTEGER NOT NULL, 
                plugin_id INTEGER DEFAULT NULL, 
                name VARCHAR(255) NOT NULL, 
                is_configurable BOOLEAN NOT NULL, 
                icon VARCHAR(255) NOT NULL, 
                is_exportable BOOLEAN NOT NULL, 
                PRIMARY KEY(id), 
                CONSTRAINT FK_76CA6C4FEC942BCF FOREIGN KEY (plugin_id) 
                REFERENCES claro_plugin (id) 
                ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
            )
        ");
        $this->addSql("
            INSERT INTO claro_widget (
                id, plugin_id, name, is_configurable, 
                icon, is_exportable
            ) 
            SELECT id, 
            plugin_id, 
            name, 
            is_configurable, 
            icon, 
            is_exportable 
            FROM __temp__claro_widget
        ");
        $this->addSql("
            DROP TABLE __temp__claro_widget
        ");
        $this->addSql("
            CREATE UNIQUE INDEX UNIQ_76CA6C4F5E237E06 ON claro_widget (name)
        ");
        $this->addSql("
            CREATE INDEX IDX_76CA6C4FEC942BCF ON claro_widget (plugin_id)
        ");
        $this->addSql("
            DROP INDEX IDX_D48CC23E44BF891
        ");
        $this->addSql("
            DROP INDEX IDX_D48CC23E7D08FA9E
        ");
        $this->addSql("
            DROP INDEX IDX_D48CC23EA76ED395
        ");
        $this->addSql("
            DROP INDEX IDX_D48CC23E82D40A1F
        ");
        $this->addSql("
            CREATE TEMPORARY TABLE __temp__claro_widget_home_tab_config AS 
            SELECT id, 
            home_tab_id, 
            user_id, 
            workspace_id, 
            widget_order, 
            type, 
            is_visible, 
            is_locked 
            FROM claro_widget_home_tab_config
        ");
        $this->addSql("
            DROP TABLE claro_widget_home_tab_config
        ");
        $this->addSql("
            CREATE TABLE claro_widget_home_tab_config (
                id INTEGER NOT NULL, 
                home_tab_id INTEGER NOT NULL, 
                user_id INTEGER DEFAULT NULL, 
                workspace_id INTEGER DEFAULT NULL, 
                widget_id INTEGER NOT NULL, 
                widget_order VARCHAR(255) NOT NULL, 
                type VARCHAR(255) NOT NULL, 
                is_visible BOOLEAN NOT NULL, 
                is_locked BOOLEAN NOT NULL, 
                PRIMARY KEY(id), 
                CONSTRAINT FK_D48CC23E7D08FA9E FOREIGN KEY (home_tab_id) 
                REFERENCES claro_home_tab (id) 
                ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, 
                CONSTRAINT FK_D48CC23EA76ED395 FOREIGN KEY (user_id) 
                REFERENCES claro_user (id) 
                ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, 
                CONSTRAINT FK_D48CC23E82D40A1F FOREIGN KEY (workspace_id) 
                REFERENCES claro_workspace (id) 
                ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, 
                CONSTRAINT FK_D48CC23EFBE885E2 FOREIGN KEY (widget_id) 
                REFERENCES claro_widget (id) 
                ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
            )
        ");
        $this->addSql("
            INSERT INTO claro_widget_home_tab_config (
                id, home_tab_id, user_id, workspace_id, 
                widget_order, type, is_visible, is_locked
            ) 
            SELECT id, 
            home_tab_id, 
            user_id, 
            workspace_id, 
            widget_order, 
            type, 
            is_visible, 
            is_locked 
            FROM __temp__claro_widget_home_tab_config
        ");
        $this->addSql("
            DROP TABLE __temp__claro_widget_home_tab_config
        ");
        $this->addSql("
            CREATE INDEX IDX_D48CC23E7D08FA9E ON claro_widget_home_tab_config (home_tab_id)
        ");
        $this->addSql("
            CREATE INDEX IDX_D48CC23EA76ED395 ON claro_widget_home_tab_config (user_id)
        ");
        $this->addSql("
            CREATE INDEX IDX_D48CC23E82D40A1F ON claro_widget_home_tab_config (workspace_id)
        ");
        $this->addSql("
            CREATE INDEX IDX_D48CC23EFBE885E2 ON claro_widget_home_tab_config (widget_id)
        ");
    }
}