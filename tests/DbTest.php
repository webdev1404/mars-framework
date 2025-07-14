<?php

use Mars\App;
use Mars\Db;

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class DbTest extends Base
{
    protected $expected = [
        1 => ['id' => 1, 'col1' => 'test1', 'col2' => 10],
        2 => ['id' => 2, 'col1' => 'test2', 'col2' => 20],
        3 => ['id' => 3, 'col1' => 'test3', 'col2' => 30],
        4 => ['id' => 4, 'col1' => 'test4', 'col2' => 40],
    ];
    protected $data = ['col1' => 'test', 'col2' => 100];

    public static function setUpBeforeClass() : void
    {
        $app = App::obj();

        $app->db->query("DROP TABLE IF EXISTS select_test");
        $app->db->query("DROP TABLE IF EXISTS insert_test");

        $app->db->query('
			CREATE TABLE select_test (
				id 	INT AUTO_INCREMENT,
				col1	varchar(255),
				col2  INT,

				PRIMARY KEY(id)
			)');
        $app->db->query("INSERT INTO select_test VALUES(1, 'test1', 10)");
        $app->db->query("INSERT INTO select_test VALUES(2, 'test2', 20)");
        $app->db->query("INSERT INTO select_test VALUES(3, 'test3', 30)");
        $app->db->query("INSERT INTO select_test VALUES(4, 'test4', 40)");

        $app->db->query('
			CREATE TABLE insert_test (
				id 	INT AUTO_INCREMENT,
				col1	varchar(255),
				col2  INT,

				PRIMARY KEY(id)
			)');
    }

    public static function tearDownAfterClass() : void
    {
        $app = App::obj();

        $app->db->query("DROP TABLE select_test");
        $app->db->query("DROP TABLE insert_test");
    }

    protected function assertRowAsObject($row, $key)
    {
        $this->assertObjectHasPropertyAndValue('id', $this->expected[$key]['id'], $row);
        $this->assertObjectHasPropertyAndValue('col1', $this->expected[$key]['col1'], $row);
        $this->assertObjectHasPropertyAndValue('col2', $this->expected[$key]['col2'], $row);
    }

    protected function assertRowAsArray($row, $key)
    {
        $this->assertArrayHasKeyAndValue('id', $this->expected[$key]['id'], $row);
        $this->assertArrayHasKeyAndValue('col1', $this->expected[$key]['col1'], $row);
        $this->assertArrayHasKeyAndValue('col2', $this->expected[$key]['col2'], $row);
    }

    protected function assertRowAsInsertedObject($row, $id)
    {
        $data = $this->data;
        $data['id'] = $id;

        $this->assertObjectHasPropertyAndValue('id', $data['id'], $row);
        $this->assertObjectHasPropertyAndValue('col1', $data['col1'], $row);
        $this->assertObjectHasPropertyAndValue('col2', $data['col2'], $row);
    }

    public function testConnection()
    {
        $this->assertEquals($this->app->db->count('select_test'), 4);
    }
    
    public function testInvalidQuery()
    {
        $this->expectException(\Exception::class);

        $this->app->db->query("SELECT col10 FROm select_test");
    }

    public function testQueryResults()
    {
        //fetch()
        $row = $this->app->db->query('SELECT * FROM select_test WHERE id = 1')->fetch();
        $this->assertRowAsObject($row, 1);

        //fetchObject()
        $row = $this->app->db->query('SELECT * FROM select_test WHERE id = 1')->fetchObject();
        $this->assertRowAsObject($row, 1);

        //fetchArray()
        $row = $this->app->db->query('SELECT * FROM select_test WHERE id = 2')->fetchArray();
        $this->assertRowAsArray($row, 2);

        //fetchRow()
        $row = $this->app->db->query('SELECT * FROM select_test WHERE id = 2')->fetchRow();
        $this->assertArrayHasKeyAndValue(0, 2, $row);
        $this->assertArrayHasKeyAndValue(1, 'test2', $row);
        $this->assertArrayHasKeyAndValue(2, '20', $row);

        //fetchColumn()
        $col1 = $this->app->db->query('SELECT * FROM select_test')->fetchColumn();
        $col2 = $this->app->db->query('SELECT * FROM select_test')->fetchColumn(1);
        $this->assertEquals($col1, 1);
        $this->assertEquals($col2, 'test1');

        //fetchAll()
        $rows = $this->app->db->query('SELECT * FROM select_test')->fetchAll();
        $this->assertCount(4, $rows);
        $this->assertRowAsObject($rows[0], 1);

        $rows = $this->app->db->query('SELECT * FROM select_test')->fetchAll(true);
        $this->assertCount(4, $rows);
        $this->assertRowAsArray($rows[0], 1);

        //fetchAllFromColumn()
        $cols = $this->app->db->query('SELECT * FROM select_test')->fetchAllFromColumn();
        $this->assertEquals($cols, [1,2,3,4]);

        $cols = $this->app->db->query('SELECT * FROM select_test')->fetchAllFromColumn(1);
        $this->assertEquals($cols, ['test1', 'test2', 'test3', 'test4']);

        //numRows
        $rows_count = $this->app->db->query('SELECT * FROM select_test')->numRows();
        $this->assertSame($rows_count, 4);

        //get() as  objects
        $rows = $this->app->db->query('SELECT * FROM select_test')->get();
        $this->assertCount(4, $rows);
        $this->assertRowAsObject($rows[0], 1);

        //get() as array
        $rows = $this->app->db->query('SELECT * FROM select_test')->get(as_array: true);
        $this->assertCount(4, $rows);
        $this->assertRowAsArray($rows[0], 1);

        //get() with a key field
        $rows = $this->app->db->query('SELECT * FROM select_test')->get('col1');
        $this->assertCount(4, $rows);
        $this->assertArrayHasKey('test1', $rows);
        $this->assertArrayHasKey('test2', $rows);
        $this->assertArrayHasKey('test3', $rows);
        $this->assertArrayHasKey('test4', $rows);
        $this->assertRowAsObject($rows['test1'], 1);

        //get() with a field and a no key field
        $rows = $this->app->db->query('SELECT * FROM select_test')->get('', 'col2');
        $this->assertCount(4, $rows);
        $this->assertEquals($rows, [10, 20, '30', 40]);

        //get() with a field and a key field
        $rows = $this->app->db->query('SELECT * FROM select_test')->get('col1', 'col2');
        $this->assertCount(4, $rows);
        $this->assertEquals($rows, ['test1' => 10, 'test2' => 20, 'test3' => 30, 'test4' => 40]);

        //getResult()
        $result = $this->app->db->query('SELECT * FROM select_test WHERE id = 2')->getResult();
        $this->assertEquals($result, 2);

        //getCount()
        $count = $this->app->db->query('SELECT COUNT(*) FROM select_test')->getCount();
        $this->assertEquals($count, 4);
    }

    public function testSelect()
    {
        //select()
        $rows = $this->app->db->select('select_test', ['id' => 30]);
        $this->assertCount(0, $rows);

        //select()
        $rows = $this->app->db->select('select_test', ['id' => 3]);
        $this->assertCount(1, $rows);
        $this->assertRowAsObject($rows[0], 3);

        //select()
        $rows = $this->app->db->select('select_test', [], 'id', 'desc');
        $this->assertCount(4, $rows);
        $this->assertRowAsObject($rows[0], 4);

        //select()
        $rows = $this->app->db->select('select_test', [], 'id', 'asc', 1, 2);
        $this->assertCount(1, $rows);
        $this->assertRowAsObject($rows[0], 3);

        //selectRow() - invalid
        $row = $this->app->db->selectRow('select_test', ['id' => 30]);
        $this->assertNull($row);

        //selectRow()
        $row = $this->app->db->selectRow('select_test', ['id' => 3]);
        $this->assertRowAsObject($row, 3);

        //selectById() - invalid
        $row = $this->app->db->selectById('select_test', 20);
        $this->assertNull($row);

        //selectById()
        $row = $this->app->db->selectById('select_test', 3);
        $this->assertRowAsObject($row, 3);

        //selectByIds()
        $rows = $this->app->db->selectByIds('select_test', [2, 3, 30], 'id', 'desc');
        $this->assertCount(2, $rows);
        $this->assertRowAsObject($row, 3);

        //selectCol()
        $vals = $this->app->db->selectCol('select_test', 'col2');
        $this->assertEquals($vals, [10,20,30,40]);

        //selectIds()
        $ids = $this->app->db->selectIds('select_test');
        $this->assertEquals($ids, [1,2,3,4]);

        //selectList()
        $list = $this->app->db->selectList('select_test', 'id', 'col1');
        $this->assertEquals($list, [1 => 'test1', 2 => 'test2', 3 => 'test3' ,4 => 'test4']);

        //selectResult
        $val = $this->app->db->selectResult('select_test', 'col1');
        $this->assertEquals($val, 'test1');
    }

    public function testCount()
    {
        //exists()
        $exists = $this->app->db->exists('select_test', ['id' => 3]);
        $this->assertTrue($exists);

        $exists = $this->app->db->exists('select_test', ['id' => [3, 4]]);
        $this->assertTrue($exists);

        $exists = $this->app->db->exists('select_test', ['id' => 30]);
        $this->assertFalse($exists);

        //count()
        $count = $this->app->db->count('select_test');
        $this->assertEquals($count, 4);

        $count = $this->app->db->count('select_test', ['id' => 3]);
        $this->assertEquals($count, 1);

        $count = $this->app->db->count('select_test', ['id' => [3, 4, 40]]);
        $this->assertEquals($count, 2);

        //countById()
        $count = $this->app->db->countById('select_test', [3, 4, 40]);
        $this->assertEquals($count, 2);
    }

    public function testInsert()
    {
        //insert()
        $this->app->db->query("TRUNCATE insert_test");

        $id = $this->app->db->insert('insert_test', $this->data);
        $this->assertGreaterThan(0, $id);

        $row = $this->app->db->query("SELECT * FROM insert_test WHERE col2 = 100")->fetch();
        $this->assertRowAsInsertedObject($row, $id);

        //insertMulti()
        $this->app->db->query("TRUNCATE insert_test");

        $data = [['col1' => 'test', 'col2' => 100], ['col1' => 'test2', 'col2' => 200]];
        $affected = $this->app->db->insertMulti('insert_test', $data);
        $this->assertEquals($affected, 2);
        $count = $this->app->db->count('insert_test');
        $this->assertEquals($count, 2);

        //update()
        $this->app->db->query("TRUNCATE insert_test");
        $id = $this->app->db->insert('insert_test', $this->data);

        $data = $this->data;
        $data['id'] = $id;
        $data['col1'] = 'abc';

        $affected = $this->app->db->update('insert_test', $data);
        $this->assertEquals($affected, 1);
        $row = $this->app->db->query("SELECT * FROM insert_test WHERE col1 = :col1", ['col1' => 'abc'])->fetch();
        $this->assertObjectHasProperty('id', $row);

        //updateById()
        $data['col1'] = 'zxc';
        $affected = $this->app->db->updateById('insert_test', $data, $id);
        $this->assertEquals($affected, 1);
        $row = $this->app->db->query("SELECT * FROM insert_test WHERE col1 = :col1", ['col1' => 'zxc'])->fetch();
        $this->assertObjectHasProperty('id', $row);

        //replace()
        $this->app->db->query("TRUNCATE insert_test");

        $data = $this->data;
        $data['id'] = 2;

        $id = $this->app->db->replace('insert_test', $data);
        $this->assertEquals($id, 2);

        $data['col1'] = '123';
        $id = $this->app->db->replace('insert_test', $data);
        $this->assertEquals($id, 2);
        $row = $this->app->db->query("SELECT * FROM insert_test WHERE id = 2")->fetch();
        $this->assertIsObject($row);
        $this->assertObjectHasPropertyAndValue('col1', '123', $row);

        //delete()
        $this->app->db->query("TRUNCATE insert_test");

        $this->app->db->insert('insert_test', $this->data);
        $affected = $this->app->db->delete('insert_test', ['col1' => 'test']);
        $count = $this->app->db->count('insert_test');
        $this->assertEquals($affected, 1);
        $this->assertEquals($count, 0);

        //deleteById()
        $id = $this->app->db->insert('insert_test', $this->data);
        $affected = $this->app->db->deleteById('insert_test', $id);
        $count = $this->app->db->count('insert_test');
        $this->assertEquals($affected, 1);
        $this->assertEquals($count, 0);

        //deleteByIds()
        $id1 = $this->app->db->insert('insert_test', $this->data);
        $id2 = $this->app->db->insert('insert_test', $this->data);
        $affected = $this->app->db->deleteByIds('insert_test', [$id1, $id2]);
        $count = $this->app->db->count('insert_test');
        $this->assertEquals($affected, 2);
        $this->assertEquals($count, 0);
    }

    public function testOtherMethods()
    {
        //getColumns()
        $cols = $this->app->db->getColumns('select_test');
        $this->assertEquals($cols, ['id' => 'int', 'col1' => 'string', 'col2' => 'int']);

        $cols = $this->app->db->getColumns('select_test', false);
        $this->assertEquals($cols, ['col1' => 'string', 'col2' => 'int']);

        //bind()
        $values = ['col1' => 'zxc', 'col2' => 456, 'col3' => 'a', 'col4' => 'b'];
        $data = $this->app->db->bind('select_test', $values);
        $this->assertEquals($data, ['col1' => 'zxc', 'col2' => 456]);

        $data = $this->app->db->bind('select_test', $values, ['col1']);
        $this->assertEquals($data, ['col2' => 456]);

        $data = $this->app->db->bind('select_test', $values, [], '456');
        $this->assertEquals($data, ['col1' => 'zxc']);

        //bindList()
        $data = $this->app->db->bindList('select_test', $values, ['id', 'col1']);
        $this->assertEquals($data, ['col1' => 'zxc']);

        //fill()
        $data = $this->app->db->fill('select_test', [], [], 1, 'test');
        $this->assertEquals($data, ['col1' => 'test', 'col2' => 1]);

        $data = $this->app->db->fill('select_test', [], [], 1, 'test', true);
        $this->assertEquals($data, ['id' => 1, 'col1' => 'test', 'col2' => 1]);

        $data = $this->app->db->fill('select_test', ['col2' => 10], [], 1, 'test', true);
        $this->assertEquals($data, ['id' => 1, 'col1' => 'test', 'col2' => 10]);

        $data = $this->app->db->fill('select_test', ['col2' => 10], ['id', 'col1'], 1, 'test', true);
        $this->assertEquals($data, ['col2' => 10]);
    }
}
