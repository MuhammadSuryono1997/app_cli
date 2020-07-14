<?php

namespace Console\Todo;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputDefinition;

class Todos extends Command
{
    protected static $defaultName = 'appTodos';

    protected function configure()
    {
        $this->setDescription("This command app todos")
        ->setName("appTodos")
        ->setDefinition(
            new InputDefinition(array(
                new InputOption("list", "l", InputOption::VALUE_NONE, "Get data todos"),
                new InputOption("add", "a", InputOption::VALUE_REQUIRED, "add Data"),
                new InputOption("update", "u", InputOption::VALUE_REQUIRED, "update data example -u '2 New data'"),
                new InputOption("delete", "d", InputOption::VALUE_REQUIRED, "delete data example -u '2 New data'")
            ))
        )
        // ->addOption("list", "l", InputOption::VALUE_OPTIONAL, "Get data todos")
        // ->addOption("add", "a", InputOption::VALUE_OPTIONAL, "add Data")
        ->setHelp("All about app todos");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $list = $input->getOption("list");
        $add = $input->getOption("add");
        $edit = $input->getOption("update");

        if ($list == 1) 
        {
            $output->writeln($this->show_data());
        }
        elseif ($add) 
        {
            $output->writeln($this->add_data($add));
        }
        elseif ($edit) 
        {
            $output->writeln($this->edit_data($edit));
        }
        
        return Command::SUCCESS;
    }

    public function get_data()
    {
        $data = file_get_contents(__DIR__."/todos.json");
        $data = json_decode($data, true);
        return $data;
    }

    public function show_data()
    {
        $data = $this->get_data()['todos'];
        $show = "Todo List\n";
        $data = array_map(function($data){return $data['id']." ".$data['title'];}, $data);
        foreach ($data as $value) 
        {
            $show .= $value."\n";
        }

        return $show;
    }

    public function add_data($new_data)
    {
        $old_data = $this->get_data();
        $old_id = count($old_data['todos']);
        $data_baru = [];

        $data_baru['id'] = $old_id + 1;
        $data_baru['title'] = $new_data;
        $data_baru['complete'] = false;
        array_push($old_data['todos'], $data_baru);
        $this->write_data($old_data);
        return true;
    }

    public function edit_data($data)
    {
        $data_filter = explode(" ",$data);
        $data = $this->get_data();
        $filter_data = array_filter($data['todos'], function($val) use($data_filter){return $val['id']==$data_filter[0];});
        foreach ($filter_data as $key => $value) 
        {
            $data['todos'][$key]['title'] = $data_filter[1];
        }
        $this->write_data($data);
    }

    public function delete_data($data)
    {
        $data_filter = explode(" ",$data);
        $data = $this->get_data();
        $filter_data = array_filter($data['todos'], function($val) use($data_filter){return $val['id']==$data_filter[0];});
        foreach ($filter_data as $key => $value) 
        {
            unset($data['todos'][$key]);
            array_values($data['todos']);
        }
        $this->write_data($data);
    }

    public function write_data($new_data)
    {
        $new_data = json_encode($new_data, JSON_PRETTY_PRINT);
        file_put_contents(__DIR__."/todos.json", $new_data);
    }
}
