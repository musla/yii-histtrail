<?php

class m110517_155003_create_tables_hist_trail extends CDbMigration
{

	/**
	 * Creates initial version of the audit trail table
	 */
	public function up()
	{

		//Create our first version of the audittrail table	
		//Please note that this matches the original creation of the 
		//table from version 1 of the extension. Other migrations will
		//upgrade it from here if we ever need to. This was done so
		//that older versions can still use migrate functionality to upgrade.
		$this->createTable( 'tbl_hist_trail',
			array(
				'id' => 'pk',
				'old_value' => 'text',
				'new_value' => 'text',
				'action' => 'string NOT NULL',
				'model' => 'NOT NULL',
				'field' => 'NOT NULL',
				'stamp' => 'datetime NOT NULL',
				'user_id' => 'string',
				'model_id' => 'string NOT NULL',
				'valid_from' => 'date',
				'valid_to' => 'date',
			)
		);

		//Index these bad boys for speedy lookups
		$this->createIndex( 'idx_hist_trail_user_id', 'tbl_hist_trail', 'user_id');
		$this->createIndex( 'idx_hist_trail_model_id', 'tbl_hist_trail', 'model_id');
		$this->createIndex( 'idx_hist_trail_model', 'tbl_hist_trail', 'model');
		$this->createIndex( 'idx_hist_trail_field', 'tbl_hist_trail', 'field');
		$this->createIndex( 'idx_hist_trail_old_value', 'tbl_hist_trail', 'old_value');
		$this->createIndex( 'idx_hist_trail_new_value', 'tbl_hist_trail', 'new_value');
		$this->createIndex( 'idx_hist_trail_action', 'tbl_hist_trail', 'action');
		$this->createIndex( 'idx_hist_trail_from', 'tbl_hist_trail', 'valid_from');
		$this->createIndex( 'idx_hist_trail_to', 'tbl_hist_trail', 'valid_to');
	}

	/**
	 * Drops the audit trail table
	 */
	public function down()
	{
		$this->dropTable( 'tbl_hist_trail' );
	}

	/**
	 * Creates initial version of the audit trail table in a transaction-safe way.
	 * Uses $this->up to not duplicate code.
	 */
	public function safeUp()
	{
		$this->up();
	}

	/**
	 * Drops the audit trail table in a transaction-safe way.
	 * Uses $this->down to not duplicate code.
	 */
	public function safeDown()
	{
		$this->down();
	}
}