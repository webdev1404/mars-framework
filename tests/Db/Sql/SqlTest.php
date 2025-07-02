<?php

include_once(dirname(__DIR__, 2) . '/Base.php');

/**
 * @ignore
 */
final class SqlTest extends Base
{
    public function setUp() : void
    {
        parent::setUp();

        $this->app->sql->start();
    }

    public function testInsert()
    {
        $table = 'test_table';

        $this->app->sql->insert($table);
        $this->assertEquals("INSERT INTO `{$table}` ", (string)$this->app->sql);
    }

    public function testUpdate()
    {
        $table = 'test_table';

        $this->app->sql->update($table);
        $this->assertEquals("UPDATE `{$table}` ", (string)$this->app->sql);
    }

    public function testReplace()
    {
        $table = 'test_table';

        $this->app->sql->replace($table);
        $this->assertEquals("REPLACE INTO `{$table}` ", (string)$this->app->sql);
    }

    public function testDelete()
    {
        $this->app->sql->delete();
        $this->assertEquals("DELETE ", (string)$this->app->sql);
    }

    public function testValues()
    {
        $values = ['id' => 1, 'name' => 'test'];
        
        $this->app->sql->values($values);
        $this->assertEquals("(`id`, `name`) VALUES (:id, :name) ", (string)$this->app->sql);
    }

    public function testValuesMulti()
    {
        $values = [['id' => 1, 'name' => 'test'], ['id' => 2, 'name' => 'test2']];
        
        $this->app->sql->valuesMulti($values);
        $this->assertEquals("(`id`, `name`) VALUES (:id_0, :name_0), (:id_1, :name_1) ", (string)$this->app->sql);
    }

    public function testSet()
    {
        $set = ['id' => 1, 'name' => 'test'];
        
        $this->app->sql->set($set);
        $this->assertEquals("SET `id` = :id, `name` = :name ", (string)$this->app->sql);
    }

    public function testSelect()
    {
        $cols = '*';

        $this->app->sql->select($cols);
        $this->assertEquals("SELECT {$cols} ", (string)$this->app->sql);
    }

    public function testSelectCount()
    {
        $this->app->sql->selectCount();
        $this->assertEquals("SELECT COUNT(*) ", (string)$this->app->sql);
    }

    public function testFrom()
    {
        $table = 'test_table';

        $this->app->sql->from($table);
        $this->assertEquals("FROM `{$table}` ", (string)$this->app->sql);
    }

    public function testJoin()
    {
        $table = 'test_table as t';
        $using = 'id';

        $this->app->sql->join($table, $using);
        $this->assertEquals("JOIN `test_table` AS t USING (`{$using}`) ", (string)$this->app->sql);

        $this->app->sql->start();
        $this->app->sql->join($table, '', 't.id = t2.id');
        $this->assertEquals("JOIN `test_table` AS t ON t.id = t2.id ", (string)$this->app->sql);
    }

    public function testLeftJoin()
    {
        $table = 'test_table as t';
        $using = 'id';

        $this->app->sql->leftJoin($table, $using);
        $this->assertEquals("LEFT JOIN `test_table` AS t USING (`{$using}`) ", (string)$this->app->sql);

        $this->app->sql->start();
        $this->app->sql->leftJoin($table, '', 't.id = t2.id');
        $this->assertEquals("LEFT JOIN `test_table` AS t ON t.id = t2.id ", (string)$this->app->sql);
    }

    public function testRightJoin()
    {
        $table = 'test_table as t';
        $using = 'id';

        $this->app->sql->rightJoin($table, $using);
        $this->assertEquals("RIGHT JOIN `test_table` AS t USING (`{$using}`) ", (string)$this->app->sql);

        $this->app->sql->start();
        $this->app->sql->rightJoin($table, '', 't.id = t2.id');
        $this->assertEquals("RIGHT JOIN `test_table` AS t ON t.id = t2.id ", (string)$this->app->sql);
    }

    public function testInnerJoin()
    {
        $table = 'test_table as t';
        $using = 'id';

        $this->app->sql->innerJoin($table, $using);
        $this->assertEquals("INNER JOIN `test_table` AS t USING (`{$using}`) ", (string)$this->app->sql);

        $this->app->sql->start();
        $this->app->sql->innerJoin($table, '', 't.id = t2.id');
        $this->assertEquals("INNER JOIN `test_table` AS t ON t.id = t2.id ", (string)$this->app->sql);
    }

    public function testWhere()
    {
        $where = ['id' => 1];

        $this->app->sql->where($where);
        $this->assertEquals("WHERE (`id` = :id) ", (string)$this->app->sql);

        $this->app->sql->start();
        $where = ['id' => [1, 2, 3]];
        $this->app->sql->where($where);
        $this->assertEquals("WHERE (`id` IN(:in_0_0, :in_0_1, :in_0_2) ) ", (string)$this->app->sql);
    }

    public function testWhereIn()
    {
        $where = ['id' => [1, 2, 3]];

        $this->app->sql->whereIn('id', [1, 2, 3]);
        $this->assertEquals("WHERE `id` IN(1, 2, 3) ", (string)$this->app->sql);
    }

    public function testHaving()
    {
        $having = ['count()' => ['operator' => '>', 'value' => 1]];

        $this->app->sql->having($having);
        $this->assertEquals("HAVING (count() > :param_0) ", (string)$this->app->sql);
    }

    public function testOrderBy()
    {
        $order_by = 'id';
        $order = 'ASC';

        $this->app->sql->orderBy($order_by, $order);
        $this->assertEquals("ORDER BY `{$order_by}` {$order} ", (string)$this->app->sql);

        $this->app->sql->start();
        $this->app->sql->orderBy($order_by);
        $this->assertEquals("ORDER BY `{$order_by}` ", (string)$this->app->sql);
    }
    
    public function testGroupBy()
    {
        $group_by = 'category';

        $this->app->sql->groupBy($group_by);
        $this->assertEquals("GROUP BY `{$group_by}` ", (string)$this->app->sql);
    }
    
    public function testLimit()
    {
        $count = 10;
        $offset = 5;

        $this->app->sql->limit($count, $offset);
        $this->assertEquals("LIMIT {$count} OFFSET {$offset} ", (string)$this->app->sql);

        $this->app->sql->start();
        $this->app->sql->limit($count);
        $this->assertEquals("LIMIT {$count} ", (string)$this->app->sql);
    }

    public function testOffset()
    {
        $offset = 5;

        $this->app->sql->offset($offset);
        $this->assertEquals("OFFSET {$offset} ", (string)$this->app->sql);
    }
}

