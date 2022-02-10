
<?php
global $wpdb;
$data = $wpdb->get_results("select day,count(day) as downloads from {$wpdb->prefix}ahm_download_stats where year='".date('Y')."' and month='".date('m')."' group by day");
$d = array_fill(1,31,0);
foreach($data as $dd){
    $d[$dd->day] = $dd->downloads;
}

?>
<style type="text/css">
#holder{
    float:left;
    background: #000;    
    margin:30px;
    padding:30px;
    -webkit-border-radius: 6px;
    -moz-border-radius: 6px;
    border-radius: 6px;    
}
</style>
<div style="clear: both;margin-left:30px">
<i><b>Download statistics for <?php echo date("F Y"); ?></b></i>
</div>
  <table id="data">
            <tfoot>

                <tr>
                    <th>1</th>
                    <th>2</th>
                    <th>3</th>
                    <th>4</th>
                    <th>5</th>

                    <th>6</th>
                    <th>7</th>
                    <th>8</th>
                    <th>9</th>
                    <th>10</th>
                    <th>11</th>

                    <th>12</th>
                    <th>13</th>
                    <th>14</th>
                    <th>15</th>
                    <th>16</th>
                    <th>17</th>

                    <th>18</th>
                    <th>19</th>
                    <th>19</th>
                    <th>20</th>
                    <th>22</th>
                    <th>23</th>

                    <th>24</th>
                    <th>25</th>
                    <th>26</th>
                    <th>27</th>
                    <th>28</th>
                    <th>29</th>

                    <th>30</th>
                    <th>31</th>
                </tr>
            </tfoot>
            <tbody>
                <tr>
                <?php for($i=1;$i<32;$i++){
                    echo "<td>{$d[$i]}</td>";
                } ?>                    

                </tr>
            </tbody>
        </table>
<div id="holder"></div>
<br>
<br>
<br>
<div style="clear: both;"></div>