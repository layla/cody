<?php namespace Layla\Cody\Cli;

use Layla\Cody\Cody;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'generate';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate code for a .yml file or a directory containing .yml files or other directories (indicating the namespace)';

	/**
	 * Create a new console command instance.
	 *
	 * @return void
	 */
	public function __construct($container)
	{
		parent::__construct();

		$this->container = $container;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$input = $this->getInput();

		$cody = $this->container->make('cody');
		$cody->setInput($input);

		$json = $this->option('json');
		if($json)
		{
			echo $cody->json();
		}

		$save = $this->option('save');
		if($save)
		{
			if( ! $cody->save($this->option('path')))
			{
				$this->error('Not enough permissions to create directories');
			}
		}

		$sync = $this->option('sync');
	}

	protected function getInput()
	{
		$path = $this->argument('path');
		$format = $this->option('format');

		$parser = $this->container->make('formats.'.$format);
		if(is_null($path))
		{
			if( ! file_exists('code.'.$format))
			{
				$this->error("You must have a code.'.$format.' file in the current directory or specify the path to the file or directory as the first argument");
			}

			$contents = $parser->parseFile('code.'.$format);
		}
		else
		{
			if(is_dir($path))
			{
				$contents = $parser->parseDir($path);
			}
			else
			{
				if( ! file_exists($path))
				{
					throw new Exception("Incorrect path: ".$path);
				}

				$contents = $parser->parseFile($path);
			}
		}

		return $contents;
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
	    return array(
	    	array('path', InputArgument::OPTIONAL, 'path to file or directory containing config code'),
	    );
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
	    return array(
	        array('format', null, InputOption::VALUE_OPTIONAL, 'Specify the input format', 'yml'),
	        array('save', null, InputOption::VALUE_NONE, 'Save code code to path', null),
	        array('path', null, InputOption::VALUE_OPTIONAL, 'Set the path for files', '.'),
	        array('json', null, InputOption::VALUE_NONE, 'Return files as JSON', null),
	        array('sync', null, InputOption::VALUE_NONE, 'Sync code with database', null),
	    );
	}

}
