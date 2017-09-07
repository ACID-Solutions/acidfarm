<?php

$cli_mode = true;
require 'init.php';

//Getting arguments
$action = arguments($argv);
$action = add_default_arguments($action);

if (isset($action['help'])) {
    echo print_cli_help();
    exit();
}

//If assistant is enabled
if (!isset($action['quickinstall'])) {
    //Launch install
    $user_prompt = strtoupper(readline("Launch install ? (Y/n)"));
    if ($user_prompt != 'N') {
        //For each step, show current configuration...
        if ($steps = get_steps()) {
            foreach ($steps as $step) {
                $config = $step . '/config.php';
                
                $inputs = [];
                if (file_exists($config)) {
                    require $config;
                }
                
                $break_step = false;
                while ($break_step === false) {
                    if ($inputs) {
                        //Printing configuration
                        echo "\n" . "\n" . "\n" . '### ' . basename($step) . ' ###' . "\n" . "\n";
                        foreach ($inputs as $key => $conf) {
                            echo print_cli_variable_detail($key, $inputs, $action);
                        }
                    }
                    
                    //... and ask for override it
                    $user_prompt = null;
                    $user_prompt = strtoupper(readline("Do you want to update it ? (Y/n)"));
                    
                    if ($user_prompt != 'N') {
                        echo "\n" . "\n" . "\n" . '### Updating ' . basename($step) . ' ###' . "\n" . "\n";
                        foreach ($inputs as $key => $conf) {
                            $title = (isset($conf['title']) ? $conf['title'] : '');
                            $desc = (isset($conf['desc']) ? $conf['desc'] : '');
                            $value = $action[$key];
                            $values = isset($conf['values']) ? $conf['values'] : (!empty($conf['bool']) ? [0, 1] : []);
                            
                            echo "\n" . print_cli_variable_detail($key, $inputs, $action) . "\n";
                            
                            if (strtoupper(readline('Do you want to this field ? [Y/n]')) != 'N') {
                                $action[$key] = readline('Please enter a value : ');
                                echo "\n";
                            }
                        }
    
                        echo "\n" . "\n" . "\n" . '# Please confirm the following informations : #' .  "\n";
                        
                    } else {
                        $break_step = true;
                    }
                }
            }
        }
    }
}

require 'treat.php';