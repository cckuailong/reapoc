<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

function mec_google_api_php_client_autoload($className)
{
    $classPath = explode('_', $className);
    
    if($classPath[0] != 'Google')
    {
        return;
    }
    
    // Drop 'Google', and maximum class file path depth in this project is 3.
    $classPath = array_slice($classPath, 1, 2);
    $filePath = dirname(__FILE__) . DS . implode(DS, $classPath) . '.php';
    
    if(file_exists($filePath))
    {
        require_once($filePath);
    }
}

spl_autoload_register('mec_google_api_php_client_autoload');