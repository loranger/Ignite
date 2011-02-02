<?php

class App extends DataObject
{

	function __construct()
	{
	}
	
	function getObjects()
	{
		return $this->getObjectsCollectionFromTable('ClassName', 'tablename', false, array('order by'=>'field asc'));
	}
	
	function getObject( $id = false )
	{
		return $this->getObjectFromId('ClassName', $id);
	}
	
	function getObjectFromOther( $other_id )
	{
		return $this->getObjectsCollectionFromTable('ClassName', 'tablename', false, array('order by'=>'field desc'));
	}

}

//##### Singleton shortcut function #####
function App()
{
	static $app;
	if ( !$app )
	{
		$app = new App();
	}
	return $app;
}

?>