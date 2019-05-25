<?php

namespace SiestaTest\Functional\Migration;

use Codeception\Util\Debug;
use Siesta\Migration\DatabaseMigrator;
use Siesta\Util\File;
use SiestaTest\TestDatabase\TestConnection;
use SiestaTest\TestUtil\DataModelHelper;

/**
 * @author Gregor Müller
 */
class MigrateStoredProcedureTest extends \PHPUnit_Framework_TestCase
{

    public function testDatabase()
    {
        $testConnection = new TestConnection();
        $testConnection->setFixtureFile(new File(__DIR__ . "/schema/migrate.storedprocedure.test.schema.json"));

        $dmh = new DataModelHelper();
        $datamodel = $dmh->readModel(__DIR__ . "/schema/migrate.storedprocedure.test.schema.xml");

        $migrator = new DatabaseMigrator($datamodel, $testConnection);
        $migrator->createAlterStatementList(true);

        $alterStatementList = $migrator->getAlterStoredProcedureStatementList();

        Debug::debug($alterStatementList);

        $this->assertCount(9, $alterStatementList);

        // new sps create statement
        $this->assertSame("create insert_new_entity", $alterStatementList[0]);
        $this->assertSame("create update_new_entity", $alterStatementList[1]);

        // change sps drop and create
        $this->assertSame("drop insert_change_entity", $alterStatementList[2]);
        $this->assertSame("create insert_change_entity", $alterStatementList[3]);
        $this->assertSame("drop update_change_entity", $alterStatementList[4]);
        $this->assertSame("create update_change_entity", $alterStatementList[5]);
        $this->assertSame("drop custom_change_entity_change_entity", $alterStatementList[6]);
        $this->assertSame("create custom_change_entity_change_entity", $alterStatementList[7]);

        // not needed anymore
        $this->assertSame("drop not_needed_anymore", $alterStatementList[8]);
    }

}