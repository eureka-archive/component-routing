<?php

/**
 * Copyright (c) 2010-2016 Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Routing;

require_once __DIR__ . '/../Route.php';
require_once __DIR__ . '/../RouteCollection.php';
require_once __DIR__ . '/../Parameter.php';
require_once __DIR__ . '/../ParameterCollection.php';

/**
 * Class Test for Routing
 *
 * @author Romain Cottard
 * @version 2.1.0
 */
class RouteTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test Parameter & ParameterCollection classes
     *
     * @return void
     * @covers ParameterCollection::__construct
     * @covers ParameterCollection::add
     * @covers ParameterCollection::getByName
     * @covers ParameterCollection::getByPosition
     * @covers Parameter::__construct
     * @covers Parameter::setValue
     * @covers Parameter::getValue
     * @covers Parameter::getName
     * @covers Parameter::getType
     */
    public function testParameter()
    {
        $id     = new Parameter(':id', Parameter::TYPE_INTEGER);
        $title  = new Parameter(':title');
        $author = new Parameter('author', Parameter::TYPE_MIXED);

        //~ ID
        $this->assertEquals($id->getName(), ':id');
        $this->assertEquals($id->getType(), Parameter::TYPE_INTEGER);
        $this->assertEquals($id->getValue(), '');

        $id->setValue('1');
        $this->assertTrue($id->getValue() === 1);

        //~ Title
        $this->assertEquals($title->getName(), ':title');
        $this->assertEquals($title->getType(), Parameter::TYPE_STRING);
        $this->assertEquals($title->getValue(), '');

        $title->setValue('mypage');
        $this->assertTrue($title->getValue() === 'mypage');

        //~ Author
        $this->assertEquals($author->getName(), ':author');
        $this->assertEquals($author->getType(), Parameter::TYPE_MIXED);
        $this->assertEquals($author->getValue(), '');

        $author->setValue('666');
        $this->assertTrue($author->getValue() === '666');

        //~ Collection
        $params = array(
            $id,
            $title,
        );

        $parameterCollection = new ParameterCollection($params);
        $parameterCollection->add($author);
        $id1 = $parameterCollection->getByName(':id');
        $id2 = $parameterCollection->getByName('id');

        $this->assertEquals($id1, $id2);

        $this->assertEquals($parameterCollection->getByPosition(0)->getName(), $id->getName());
        $this->assertEquals($parameterCollection->getByPosition(1)->getName(), $title->getName());
        $this->assertEquals($parameterCollection->getByPosition(2)->getName(), $author->getName());

        //$parameterCollection->
    }

    /**
     * Test Route class
     *
     * @return void
     * @covers Route::__construct
     * @covers Route::build
     * @covers Route::getParameterCollection
     * @covers Route::verify
     * @covers Route::getActionName
     * @covers Route::getControllerName
     * @covers Route::getName
     * @covers RouteCollection::__construct
     * @covers RouteCollection::match
     * @covers RouteCollection::add
     */
    public function testRoute()
    {
        $id   = new Parameter(':id', Parameter::TYPE_INTEGER);
        $name = new Parameter(':name');
        $any  = new Parameter(':name', Parameter::TYPE_MIXED);

        $routes   = array();
        $routes[] = new Route('test1', '/news/list', 'BlogController');
        $routes[] = new Route('test2', '/news/view', 'BlogController::view');
        $routes[] = new Route('test3', '/news/view/:id', 'BlogController::', array(clone $id));
        $routes[] = new Route('test4', '/news/view/:id-:name', 'BlogController::view', array(clone $id));
        $routes[] = new Route('test5', '/news/edit/:id-:name.html', 'AnotherController::edit',
            array(clone $id, clone $name));
        $routes[] = new Route('test6', '/news/list/:id-:name/', 'BlogController::list', array(clone $id, clone $any));

        $collection = RouteCollection::getInstance($routes);

        $route = $collection->match('http://eureka-framework.com/news/list?view=block');
        $this->assertEquals($route->getName(), 'test1');
        $this->assertEquals($route->getControllerName(), 'BlogController');
        $this->assertEquals($route->getActionName(), 'index');

        $route = $collection->match('http://eureka-framework.com/news/list');
        $this->assertEquals($route->getName(), 'test1');
        $this->assertEquals($route->getControllerName(), 'BlogController');
        $this->assertEquals($route->getActionName(), 'index');

        $route = $collection->match('http://eureka-framework.com/news/view');
        $this->assertEquals($route->getName(), 'test2');
        $this->assertEquals($route->getControllerName(), 'BlogController');
        $this->assertEquals($route->getActionName(), 'view');

        $route = $collection->match('http://eureka-framework.com/news/view/5');
        $this->assertEquals($route->getName(), 'test3');
        $this->assertTrue($route->getParameterCollection()->getByName('id')->getValue() === 5);
        $this->assertEquals($route->getControllerName(), 'BlogController');
        $this->assertEquals($route->getActionName(), 'index');

        $route = $collection->match('http://eureka-framework.com/news/edit/6-pagenewtitle.html');
        $this->assertEquals($route->getName(), 'test5');
        $this->assertEquals($route->getParameterCollection()->getByName('id')->getValue(), 6);
        $this->assertEquals($route->getParameterCollection()->getByName('name')->getValue(), 'pagenewtitle');
        $this->assertEquals($route->getControllerName(), 'AnotherController');
        $this->assertEquals($route->getActionName(), 'edit');

        $route = $collection->match('http://eureka-framework.com/news/edit/6-my-newhome.test.html');
        $this->assertTrue(!($route instanceof Route));

        $route = $collection->match('http://eureka-framework.com/news/view/6-my-newhome');
        $this->assertEquals($route->getName(), 'test4');
        $this->assertEquals($route->getControllerName(), 'BlogController');
        $this->assertEquals($route->getActionName(), 'view');

        $route = $collection->match('http://eureka-framework.com/news/view/6-my-newhome.test');
        $this->assertTrue(!($route instanceof Route));

        $route = $collection->match('http://eureka-framework.com/news/list/6-my-newhome.test/');
        $this->assertEquals($route->getName(), 'test6');
        $this->assertEquals($route->getControllerName(), 'BlogController');
        $this->assertEquals($route->getActionName(), 'list');

        $route = $collection->match('http://eureka-framework.com/news/view/6-my-newhome_test');
        $this->assertEquals($route->getName(), 'test4');
        $this->assertEquals($route->getControllerName(), 'BlogController');
        $this->assertEquals($route->getActionName(), 'view');
    }
}
