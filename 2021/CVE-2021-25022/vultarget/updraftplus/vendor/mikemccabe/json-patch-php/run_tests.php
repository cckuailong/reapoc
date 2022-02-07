<?php

// This is a simple jig for testing JsonPatch.inc against json-encoded
// test files.

require 'vendor/autoload.php';

use mikemccabe\JsonPatch\JsonPatch;

$verbose = false;

function print_test($test)
{
  print "{ ";
  $first = true;
  foreach(array('comment', 'doc', 'patch', 'expected', 'error') as $key)
  {
    if (array_key_exists($key, $test))
    {
      if (!$first)
      {
        print ",\n  ";
      }
      $first = false;
      print("\"$key\": " . json_encode($test[$key]));
    }
  }
  print " }\n";
}


function do_test($test, $simplexml_mode=false)
{
  global $verbose;
  // Allow 'comment-only' test records
  if (!(isset($test['doc']) && isset($test['patch'])))
    return true;
  try
  {
    $patched =  JsonPatch::patch($test['doc'], $test['patch'], $simplexml_mode);

    if (isset($test['error']))
    {
      print("test failed: expected error didn't occur\n");
      print_test($test);
      print("found: ");
      print json_encode($patched);
      print("\n\n");
    }

    if (!isset($test['expected']))
    {
      return true;
    }

    if (!JsonPatch::considered_equal($patched, $test['expected']))
    {
      print("test failed:\n");
      print_test($test);
      print("found: " . json_encode($patched) . "\n\n");
      return false;
    }
    else
    {
      if ($verbose && array_key_exists('comment', $test))
      {
        print "OK: " . $test['comment'] . "\n\n";
      }
      return true;
    }
  }
  catch (Exception $ex)
  {
    if (!isset($test['error']))
    {
      print("test failed with exception: " . $ex->getMessage() . "\n");
      print_test($test);
      print("\n");
      return false;
    }
    else
    {
      if ($verbose)
      {
        if (array_key_exists('comment', $test))
        {
          print "OK: " . $test['comment'] . "\n";
        }
        print("caught:   " . $ex->getMessage() . "\n");
        print("expected: " . $test['error'] . "\n\n");
      }
      return true;
    }
  }
}      


// Piggyback on patch tests to test diff as well - use 'doc' and
// 'expected' from testcases.  Generate a diff, apply it, and check
// that it matches the target - in both directions.
function diff_test($test)
{
  // Skip comment-only or test op tests
  if (!(isset($test['doc']) && isset($test['expected'])))
  {
     return true;
  }

  $result = true;
  try
  {
    $doc1 = $test['doc']; // copy, in case sort/patch alters
    $doc2 = $test['expected'];
    $patch = JsonPatch::diff($doc1, $doc2);
    $patched = JsonPatch::patch($doc1, $patch);
    if (!JsonPatch::considered_equal($patched, $doc2))
    {
      print("diff test failed:\n");
      print_test($test);
      print("from:     " . json_encode($doc1) . "\n");
      print("diff:     " . json_encode($patch) . "\n");
      print("found:    " . json_encode($patched) . "\n");
      print("expected: " . json_encode($doc2) . "\n\n");
      $result = false;
    }
    
    // reverse order
    $doc1 = $test['expected']; // copy, in case sort/patch alters
    $doc2 = $test['doc'];
    $patch = JsonPatch::diff($doc1, $doc2);
    $patched = JsonPatch::patch($doc1, $patch);
    if (!JsonPatch::considered_equal($patched, $doc2))
    {
      print("reverse diff test failed:\n");
      print_test($test);
      print("from:     " . json_encode($doc1) . "\n");
      print("diff:     " . json_encode($patch) . "\n");
      print("found:    " . json_encode($patched) . "\n");
      print("expected: " . json_encode($doc2) . "\n\n");
      $result = false;
    }
  }
  catch (Exception $ex)
  {
    print("caught exception ".$ex->getMessage()."\n");
    return false;
  }
  return $result;
}


function test_file($filename, $simplexml_mode=false)
{
  $testfile = file_get_contents($filename);
  if (!$testfile)
  {
    throw new Exception("Couldn't find test file $filename");
    return false;
  }

  $tests = json_decode($testfile, 1);
  if (is_null($tests))
  {
    throw new Exception("Error json-decoding test file $filename");
  }

  $success = true;
  foreach ($tests as $test)
  {
    if (isset($test['disabled']) && $test['disabled'])
    {
      continue;
    }
    if (!do_test($test, $simplexml_mode))
    {
      $success = false;
    }
    if (!$simplexml_mode && !diff_test($test))
    {
      $success = false;
    }
  }
  return $success;
}


function main()
{
  $result = true;
  $testfiles = array(
                     'local_tests.json',
                     'json-patch-tests/tests.json',
                     'json-patch-tests/spec_tests.json'
                     );
  foreach ($testfiles as $testfile)
  {
    if (!test_file($testfile))
    {
      $result = false;
    }
  }
  if (!test_file('simplexml_tests.json', true))
  {
    $result = false;
  }
  return $result;
}


if (!main())
{
  exit(1);
}
else
{
  exit(0);
}