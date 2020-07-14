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
                new InputOption("update", "u", InputOption::VALUE_REQUIRED, "update data example -u --update '2 New data'"),
                new InputOption("delete", "d", InputOption::VALUE_REQUIRED, "delete data example -d or --delete '2'"),
                new InputOption("clear", "c", InputOption::VALUE_NONE, "clear all data"),
                new InputOption("done", "f", InputOption::VALUE_REQUIRED, "update status example -f '8'"),
                new InputOption("undone", "z", InputOption::VALUE_REQUIRED, "update status example -z or --undone '8'")
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
        $delete = $input->getOption("delete");
        $clear = $input->getOption("clear");
        $done = $input->getOption("done");
        $undone = $input->getOption("undone");

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
        elseif ($delete) 
        {
            $output->writeln($this->delete_data($delete));
        }
        elseif ($clear == 1) 
        {
            $output->writeln($this->clear_data());
        }
        elseif ($done) 
        {
            $output->writeln($this->done_data($done));
        }
        elseif ($undone) 
        {
            $output->writeln($this->undone_data($undone));
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
        $data = array_map(function($data){return $data['id']." ".$data['title'].$data['complete'];}, $data);
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
        $data_baru['complete'] = "";
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
            $data['todos'][$key]['title'] = join(" ",array_slice($data_filter, 1,count($data_filter)));
            echo join(" ", array_slice($data_filter, 1,count($data_filter)));
        }
        $this->write_data($data);
    }

    public function done_data($data)
    {
        $data_filter = explode(" ",$data);
        $data = $this->get_data();
        $filter_data = array_filter($data['todos'], function($val) use($data_filter){return $val['id']==$data_filter[0];});
        foreach ($filter_data as $key => $value) 
        {
            if($data['todos'][$key]['complete'] == "")
            {
                $data['todos'][$key]['complete'] = "(DONE)";
            }
        }
        $this->write_data($data);
    }

    public function undone_data($data)
    {
        $data_filter = explode(" ",$data);
        $data = $this->get_data();
        $filter_data = array_filter($data['todos'], function($val) use($data_filter){return $val['id']==$data_filter[0];});
        foreach ($filter_data as $key => $value) 
        {
            if ($data['todos'][$key]['complete'] == "(DONE)") 
            {
                $data['todos'][$key]['complete'] = "";
            }
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
            array_splice($data['todos'], $key, 1);
        }
        $this->write_data($data);
    }

    public function clear_data()
    {
        $clear_data = ["todos"=> []];
        $this->write_data($clear_data);
    }

    public function write_data($new_data)
    {
        $new_data = json_encode($new_data, JSON_PRETTY_PRINT);
        file_put_contents(__DIR__."/todos.json", $new_data);
    }
}
