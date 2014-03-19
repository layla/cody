<?php namespace Layla\Cody\Compilers\Php\Laravel;

use Layla\Cody\Compilers\Php\Core\ClassCompiler;

use Layla\Cody\Compilers\Php\Laravel\Migration\Change;

class MigrationCompiler extends ClassCompiler {

	protected $previousVersion;

	public function setup()
	{
		$this->addProperty('description', array(
			'type' => 'array',
			'comment' => 'The update message people see in the toolbox when updating',
			'value' => $this->get('description', 'No update message available')
		));

		$this->addMethod('up', array(
			'returnType' => 'void',
			'comment' => 'Make changes to the database.',
		), $this->getUpMethodBody());

		$this->addMethod('down', array(
			'returnType' => 'void',
			'comment' => 'Revert the changes to the database.',
		), $this->getDownMethodBody());
	}

	protected function getChangesForUp()
	{
		$changes = array();

		$oldColumns = array();
		if($this->previousVersion)
		{
			$oldColumns = $this->previousVersion->get('columns', array());
		}

		$oldColumnNames = array_keys($oldColumns);
		$newColumns = $this->get('columns', array());

		foreach($newColumns as $name => $configuration)
		{
			if(array_key_exists('previous_name', $configuration))
			{
				// rename
				$changes[] = new Change('renameColumn', $name, array(
					'old' => $configuration['previous_name'],
					'new' => $name
				));
			}
			elseif(in_array($name, $oldColumnNames))
			{
				// already exists, update configuration if necesarry
				// @todo implement
			}
			else
			{
				// new columns, add it
				$changes[] = new Change('createColumn', $name, $configuration);
			}
		}

		return $changes;
	}

	protected function getChangesForDown()
	{
		$changes = array();

		$oldColumns = array();
		if($this->previousVersion)
		{
			$oldColumns = $this->previousVersion->get('columns', array());
		}

		$oldColumnNames = array_keys($oldColumns);
		$newColumns = $this->get('columns', array());

		foreach($newColumns as $name => $configuration)
		{
			if(array_key_exists('previous_name', $configuration))
			{
				// rename
				$changes[] = new Change('renameColumn', $name, array(
					'new' => $configuration['previous_name'],
					'old' => $name
				));
			}
			elseif(in_array($name, $oldColumnNames))
			{
				// already exists, update configuration if necesarry
				// @todo implement
			}
			else
			{
				// new columns, add it
				$changes[] = new Change('dropColumn', $name, $configuration);
			}
		}

		return $changes;
	}

	protected function getUpMethodBody()
	{
		return $this->compileChanges($this->getChangesForUp());
	}

	protected function getDownMethodBody()
	{
		return $this->compileChanges($this->getChangesForDown());
	}

	protected function compileChanges($changes)
	{
		$parts = array();
		foreach($changes as $change)
		{
			$parts[]  = $change->compile();
		}

		return implode('', $parts);
	}

	protected function getAllColumns()
	{
		$columns = $this->get('columns', array());
		$relations = $this->get('relations', array());

		$columns = $columns + $this->getColumnsForRelations($relations);
	}

	protected function getColumnsForRelations($relations)
	{
		foreach($relations as $relation)
		{
			// $relation['other']
			dd('relation', $relation);
		}
	}

}
