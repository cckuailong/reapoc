<?php

abstract class WPAM_Pages_Admin_AdminPage
{
	private $name;
	private $menuName;
	private $id;
	private $requiredCap;
	private $children;

	public function getName() { return $this->name; }
	public function getMenuName() { return $this->menuName; }
	public function getId() { return $this->id; }
	public function getRequiredCap() { return $this->requiredCap; }

	public function __construct($id, $name, $menuName, $requiredCap, $children = array())
	{
		$this->name = $name;
		$this->id = $id;
		$this->menuName = $menuName;
		$this->requiredCap = $requiredCap;
		$this->children = $children;
	}

	public function addChild(WPAM_Pages_Admin_AdminPage $page)
	{
		$this->children[] = $page;
	}

	public function getChildren()
	{
		return $this->children;
	}

	public function process()
	{
		$outputCleaner = new WPAM_OutputCleaner();

		$response = $this->processRequest($outputCleaner->cleanRequest($_REQUEST));
		echo $response->render();
	}

	public abstract function processRequest($request);
}

?>
