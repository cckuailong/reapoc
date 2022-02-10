<?php
// phpcs:disable Generic.Arrays.DisallowLongArraySyntax.Found

require_once '../vendor/autoload.php';

use ICal\ICal;

try {
    $ical = new ICal('ICal.ics', array(
        'defaultSpan'                 => 2,     // Default value
        'defaultTimeZone'             => 'UTC',
        'defaultWeekStart'            => 'MO',  // Default value
        'disableCharacterReplacement' => false, // Default value
        'skipRecurrence'              => false, // Default value
        'useTimeZoneWithRRules'       => false, // Default value
    ));
    // $ical->initFile('ICal.ics');
    // $ical->initUrl('https://raw.githubusercontent.com/u01jmg3/ics-parser/master/examples/ICal.ics');
} catch (\Exception $e) {
    die($e);
}

$forceTimeZone = false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <title>PHP ICS Parser example</title>
    <style>body { background-color: #eee } .caption { overflow-x: auto }</style>
</head>
<body>
<div class="container-fluid">
    <h3>PHP ICS Parser example</h3>
    <ul class="list-group">
        <li class="list-group-item">
            <span class="badge"><?php echo $ical->eventCount ?></span>
            The number of events
        </li>
        <li class="list-group-item">
            <span class="badge"><?php echo $ical->freeBusyCount ?></span>
            The number of free/busy time slots
        </li>
        <li class="list-group-item">
            <span class="badge"><?php echo $ical->todoCount ?></span>
            The number of todos
        </li>
        <li class="list-group-item">
            <span class="badge"><?php echo $ical->alarmCount ?></span>
            The number of alarms
        </li>
    </ul>

    <?php
        $showExample = array(
            'interval' => true,
            'range'    => true,
            'all'      => true,
        );
    ?>

    <?php
        if ($showExample['interval']) {
            $events = $ical->eventsFromInterval('1 week');

            if ($events) {
                echo '<h4>Events in the next 7 days:</h4>';
            }

            $count = 1;
    ?>
    <div class="row">
    <?php
    foreach ($events as $event) : ?>
        <div class="col-md-4">
            <div class="thumbnail">
                <div class="caption">
                    <h3><?php
                        $dtstart = $ical->iCalDateToDateTime($event->dtstart_array[3], $forceTimeZone);
                        echo $event->summary . ' (' . $dtstart->format('d-m-Y H:i') . ')';
                    ?></h3>
                    <?php echo $event->printData() ?>
                </div>
            </div>
        </div>
        <?php
            if ($count > 1 && $count % 3 === 0) {
                echo '</div><div class="row">';
            }

            $count++;
        ?>
    <?php
    endforeach
    ?>
    </div>
    <?php } ?>

    <?php
        if ($showExample['range']) {
            $events = $ical->eventsFromRange('2017-03-01 12:00:00', '2017-04-31 17:00:00');

            if ($events) {
                echo '<h4>Events March through April:</h4>';
            }

            $count = 1;
    ?>
    <div class="row">
    <?php
    foreach ($events as $event) : ?>
        <div class="col-md-4">
            <div class="thumbnail">
                <div class="caption">
                    <h3><?php
                        $dtstart = $ical->iCalDateToDateTime($event->dtstart_array[3], $forceTimeZone);
                        echo $event->summary . ' (' . $dtstart->format('d-m-Y H:i') . ')';
                    ?></h3>
                    <?php echo $event->printData() ?>
                </div>
            </div>
        </div>
        <?php
            if ($count > 1 && $count % 3 === 0) {
                echo '</div><div class="row">';
            }

            $count++;
        ?>
    <?php
    endforeach
    ?>
    </div>
    <?php } ?>

    <?php
        if ($showExample['all']) {
            $events = $ical->sortEventsWithOrder($ical->events());

            if ($events) {
                echo '<h4>All Events:</h4>';
            }
    ?>
    <div class="row">
    <?php
    $count = 1;
    foreach ($events as $event) : ?>
        <div class="col-md-4">
            <div class="thumbnail">
                <div class="caption">
                    <h3><?php
                        $dtstart = $ical->iCalDateToDateTime($event->dtstart_array[3], $forceTimeZone);
                        echo $event->summary . ' (' . $dtstart->format('d-m-Y H:i') . ')';
                    ?></h3>
                    <?php echo $event->printData() ?>
                </div>
            </div>
        </div>
        <?php
            if ($count > 1 && $count % 3 === 0) {
                echo '</div><div class="row">';
            }

            $count++;
        ?>
    <?php
    endforeach
    ?>
    </div>
    <?php } ?>
</div>
</body>
</html>
