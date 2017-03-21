<?php

/*
 * Yaf bootstrap
 * Link http://github.com/typefo/pbx-mon
 * By typefo <typefo@qq.com>
 */

class Bootstrap extends Yaf\Bootstrap_Abstract{

    public function _initConfig() {
		$this->config = Yaf\Application::app()->getConfig();
		Yaf\Registry::set('config', $this->config);
	}

	public function _initDatabase() {
	    $host = $this->config->db->host;
	    $port = $this->config->db->port;
	    $user = $this->config->db->user;
	    $pass = $this->config->db->pass;
	    $name = $this->config->db->name;

	    try {
	        Yaf\Registry::set('db', new PDO('mysql:host=' . $host . ';port=' . $port . ';dbname=' . $name, $user, $pass));
	    } catch (PDOException $e) {
	        error_log($e->getMessage(), 0);
	    }

	}

	public function _initSession(Yaf\Dispatcher $dispatcher) {
	    $session = Yaf\Session::getInstance();
            Yaf\Registry::set('session', $session);
            $session->start();
	}

	public function _initAuth(Yaf\Dispatcher $dispatcher) {
		$authPlugin = new AuthPlugin();
		$dispatcher->registerPlugin($authPlugin);
	}
}
