<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
?><table class="striped">
	<tr>
		<th><?php _e('No.','wp-stats-manager'); ?></th>
		<th><?php _e('Shortcode','wp-stats-manager'); ?></th>
		<th><?php _e('Attributes','wp-stats-manager'); ?></th>
		<th><?php _e('Description','wp-stats-manager'); ?></th>
		</tr>
	<tr>
		<td>1</td>
		<td>[<?php echo WSM_PREFIX ?>_showDayStats]</td>
		<td>-</td>
		<td>This​ shortcode shows total statistics​ for ‘Today’​ or the current day.. It shows Total​ Page Views,​ Visitors,​ First​ Time​ Visitors, Online​ users,​ Average​ Visit​ Length​ for​ current​ day. </td>
		
	</tr>
	<tr>
		<td>2</td>
		<td>[<?php echo WSM_PREFIX ?>_showDayStatBox]</td>
		<td><b>Type​ :</b> Hourly,​ Daily,​ Monthly <br/><b>Condition​ :</b> Normal,​ Range,​ Compare <br/><b>From :​ </b>​ Start​ Date <br/><b>To :​ </b>​ End​ Date <br/><b>First​ : </b>​ First​ date​ for​ comparison​ (Compare) <br/><b>Second​ : </b>​ second​ date​ for​ comparison (Compare)<br/><b>Example : </b> <br/>[wsm_showDayStatBox type="Hourly" condition="Normal" from="2017-08-29" to="2017-08-29"] </td>
		<td>It​ shows​ Total​ Page​ Views,​ Visitors,​ First​ Time​ Visitors, Page​ Views​ Per​ Visit,​ New​ Visitors​ Ratio​ as​ per selected​ criteria.​ It​ will​ display​ stats​ hourly,​ daily​ and compare​ between​ two​ dates. </td>
	</tr>
	<tr>
		<td>3</td>
		<td>[<?php echo WSM_PREFIX ?>_showGenStats]</td>
		<td>-</td>
		<td>It​ shows​ Total​ PageViews,​ Total​ Visitors,​ Page​ Views Per​ Visit​ till​ the​ date​ from​ the​ last​ hit​ time.</td>
	</tr>
	<tr>
		<td>4</td>
		<td>[<?php echo WSM_PREFIX ?>_showLastDaysStats]</td>
		<td><b>Days​ : </b>​ Number​ of​ days<br/><b>Example : </b><br/>[wsm_showLastDaysStats days="4"]</td>
		<td>It​ shows​ the​ total​ number​ of​ Page​ Views,​ Visitors,​ First Time​ Visitors​ and​ Pageviews​ Per​ Visit​ from​ the​ last number​ of​ days. </td>
	</tr>
	<tr>
		<td>5</td>
		<td>[<?php echo WSM_PREFIX ?>_showForeCast]</td>
		<td>-</td>
		<td>It​ shows​ forecast​ statistics​ for​ today​ based​ on​ historical data. <br/>1. Current​ Hour​ visitors. <br/>2. Change​ in​ percentage​ (Current​ Hour​ visitors) based​ on​ last​ 7 days​ ago​ visitors​ in​ same​ hour. <br/>3. Change​ in​ percentage​ (Current​ Hour​ visitors) based​ on​ last​ 14​ days​ for​ visitors​ on​ the​ same hour. <br/>4. Current​ Day​ Visitors. <br/>5. Change​ in​ percentage​ (Current​ Day​ visitors) based​ on​ last​ 7 days​ ago​ visitors. <br/>6. Change​ in​ percentage​ (Current​ Day​ visitors) based​ on​ last​ 14​ days​ data.. </td>
	</tr>
	<tr>
		<td>6</td>
		<td>[<?php echo WSM_PREFIX ?>_showGeoLocation]</td>
		<td>-</td>
		<td>It​ show​ total​ Page​ views​ by​ country​ in​ descending order​ with​ pie​ chart.​ It​ shows​ these​ stats​ for​ top​ 10 countries. </td>
	</tr>
	<tr>
		<td>7</td>
		<td>[<?php echo WSM_PREFIX ?>_showCurrentStats]</td>
		<td>-</td>
		<td>It​ shows​ bar​ chart​ including​ following​ details: <br />1. Hourly​ Page​ views,​ Visitors,​ first​ time​ visitors, Bounce​ rate,​ Page​ views​ per​ visit,​ New​ Visitors (%),​ Online​ users. <br />2. It​ shows​ Yesterdays​ Hourly​ Page​ views, Visitors,​ and​ first​ time​ visitors <br />3. Shows​ statistics​ for​ the​ past​ 7 days​ - Hourly Page​ views,​ Visitors,​
		 and​ first​ time​ visitors​ for same​ day <br />4. Shows​ Stats​ for​ the​ past​ 14​ - Hourly​ Page​ views, Visitors,​ and​ first​ time​ visitors​ for​ same​ day </td>
	</tr>
	<tr>
		<td>8</td>
		<td>[<?php echo WSM_PREFIX ?>_showDayStatsGraph]</td>
		<td><b>Type​ : </b>​ Hourly,​ Daily,​ Monthly <br/><b>Condition​ : </b>​ Normal,​ Range,​ Compare <br/><b>From :​</b> Start​ Date <br/><b>To :​ </b> End​ Date <br/><b>First​ : </b>​ First​ date​ for​ comparison​ (Compare) <br/><b>Second​ : </b>​ second​ date​ for​ comparison (Compare) <br/><b>Example : </b><br/>[wsm_showDayStatsGraph type="Hourly" condition="Normal" from="2017-08-29" to="2017-08-29"]</td>
		<td>It​ shows​ bar​ chart​ based​ on​ selected​ date​ criterias <br/>1. Hourly​ Page​ views <br/>2. Hourly​ Visitors <br/>3. Hourly​ first​ time​ visitors <br/>4. Hourly​ Bounce​ rate <br/>5. Hourly​ Page​ views​ per​ visit <br/>6. Hourwise​ New​ Visitors​ (%) <br/>7. Hourwise​ Online​ users. </td>
	</tr>
	<tr>
		<td>9</td>
		<td>[<?php echo WSM_PREFIX ?>_showLastDaysStatsChart]</td>
		<td>-</td>
		<td>It​ shows​ a line​ chart​ for​ day​ wise​ statistics​ for​ selected number​ of​ days. Daywise​ Page​ views,​ Visitors,​ first​ time​ visitors,​ Bounce rate,​ Page​ views​ per​ visit,​ New​ Visitors​ (%),​ Online users​ statistics​ are​ displayed. </td>
	</tr>
	<tr>
		<td>10</td>
		<td>[<?php echo WSM_PREFIX ?>_showRecentVisitedPages]</td>
		<td><b>Limit​ :</b> Number​ of​ records​ to​ display. <br/><b>Example : </b><br/>[wsm_showRecentVisitedPages limit="2"]</td>
		<td>It​ shows​ recently​ visited​ pages​ with​ city,​ country, browser​ and​ Operating​ System​ information. </td>
	</tr>
	<tr>
		<td>11</td>
		<td>[<?php echo WSM_PREFIX ?>_showPopularPages]</td>
		<td><b>Limit​ :</b> Number​ of​ records​ to​ display. <br/><b>Example : </b><br/>[wsm_showPopularPages limit="2"]</td>
		<td>Shows​ most​ popular​ pages. </td>
	</tr>
	<tr>
		<td>12</td>
		<td>[<?php echo WSM_PREFIX ?>_showPopularReferrers]</td>
		<td><b>Limit​ :</b> Number​ of​ records​ to​ display. <br/><b>Example : </b><br/>[wsm_showPopularReferrers limit="2"]</td>
		<td>Shows​ most​ popular​ referrer​ list. </td>
	</tr>
	<tr>
		<td>13</td>
		<td>[<?php echo WSM_PREFIX ?>_showMostActiveVisitors]</td>
		<td><b>Limit​ :</b> Number​ of​ records​ to​ display. <br/><b>Example : </b><br/>[wsm_showMostActiveVisitors limit="2"]</td>
		<td>Shows​ the​ most​ active​ visitor​ list​ with​ their​ ipaddress, Country,​ Operating​ System,​ Browser​ and​ Device information. </td>
	</tr>
	<tr>
		<td>14</td>
		<td>[<?php echo WSM_PREFIX ?>_showMostActiveVisitorsGeo]</td>
		<td>-</td>
		<td>Shows​ active​ visitors​ within​ the​ google​ map​ based​ on visitor's​ ip​ address. </td>
	</tr>
	<tr>
		<td>15</td>
		<td>[<?php echo WSM_PREFIX ?>_showActiveVisitorsByCountry]</td>
		<td>-</td>
		<td>Show​ the​ total​ number​ of​ current​ active​ visitors countrywise. </td>
	</tr>
	<tr>
		<td>16</td>
		<td>[<?php echo WSM_PREFIX ?>_showActiveVisitorsByCity]</td>
		<td>-</td>
		<td>Shows​ the​ total​ number​ of​ current​ active​ visitors​ by City </td>
	</tr>
	<tr>
		<td>17</td>
		<td>[<?php echo WSM_PREFIX ?>_showRecentVisitedPagesDetails]</td>
		<td>-</td>
		<td>Shows​ the​ list​ of​ recently​ visited​ pages​ by​ the​ visitors along​ with​ ip​ address,​ URL,​ number​ of​ hits,​ Country, City,​ Browser,​ Operating​ System​ and​ Screen resolution. </td>
	</tr>
	<tr>
		<td>18</td>
		<td>[<?php echo WSM_PREFIX ?>_showTrafficStatsList]</td>
		<td><b>Type​ : </b>​ Hourly,​ Daily,​ Monthly <br/><b>Condition​ : </b>​ Normal,​ Range,​ Compare <br/><b>From :​ </b>​ Start​ Date <br/><b>To :​</b> End​ Date <br/><b>First​ : </b>​ First​ date​ for​ comparison​ (Compare) <br/><b>Second​ : </b>​ second​ date​ for​ comparison (Compare) <br/><b>Example : </b><br/>[wsm_showTrafficStatsList type="Hourly" condition="Normal" from="2017-08-29" to="2017-08-29"]</td>
		<td>Shows​ hour​ wise​ Detailed​ statistics​ on​ selected​ date, date​ range​ or​ both​ dates​ for​ comparison. Details​ includes: <br/>1.Visit​ time​ range <br/>2.Visitors​ Graph <br/>3.Visitors <br/>4.Pages​ per​ visit <br/>5.New​ Visitors​ percentage <br/>6.Bounce​ Rate </td>
	</tr>
	<tr>
		<td>19</td>
		<td>[<?php echo WSM_PREFIX ?>_showRefferStatsBox]</td>
		<td><b>Type​ : </b>​ Hourly,​ Daily,​ Monthly <br/><b>Condition​ : </b>​ Normal,​ Range,​ Compare<br/><b>From :​ </b>​ Start​ Date <br/><b>To :​</b> End​ Date <br/><b>First​ : </b>​ First​ date​ for​ comparison​ (Compare) <br/><b>Second​ : </b>​ second​ date​ for​ comparison (Compare)<br/><b>Example : </b><br/>[wsm_showRefferStatsBox type="Hourly" condition="Normal" from="2017-08-29" to="2017-08-29"]</td>
		<td>Show​ referrer​ statistics​ with​ general​ statistics​ based​ on selected​ date​ or​ date​ range. It​ shows​ Total​ page​ views,​ visitors,​ New​ visitors,​ Page views​ per​ visit​ against​ referrer​ statistics. </td>
	</tr>
	<tr>
		<td>20</td>
		<td>[<?php echo WSM_PREFIX ?>_showTopReferrerList]</td>
		<td><b>Type​ : </b> Hourly,​ Daily,​ Monthly <br/><b>Condition​ : </b> Normal,​ Range,​ Compare<br/><b>From :​ </b>​ Start​ Date <br/><b>To :​</b> End​ Date <br/><b>First​ : </b>​ First​ date​ for​ comparison​ (Compare) <br/><b>Second​ : </b>​ second​ date​ for​ comparison (Compare) <br/><b>Example : </b><br/>[wsm_showTopReferrerList type="Hourly" condition="Normal" from="2017-08-29" to="2017-08-29"]</td>
		<td>Shows​ a list​ of​ top​ referrers​ in​ descending​ order,​ with the​ details​ of​ visited​ pages​ as​ well​ last​ 30​ days cumulative​ data​ with​ graph. </td>
	</tr>
	<tr>
		<td>21</td>
		<td>[<?php echo WSM_PREFIX ?>_showSearchEngineSummary]</td>
		<td><b>Type​ : </b>​ Hourly,​ Daily,​ Monthly <br/><b>Condition​ : </b>​ Normal,​ Range,​ Compare<br/><b>From :​ </b>​ Start​ Date <br/><b>To :​</b> End​ Date <br/><b>First​ : </b>​ First​ date​ for​ comparison​ (Compare) <br/><b>Second​ : </b>​ second​ date​ for​ comparison (Compare) <br/><b>Example : </b><br/>[wsm_showSearchEngineSummary type="Hourly" condition="Normal" from="2017-08-20" to="2017-08-20"]</td>
		<td>Shows​ a list​ of​ top​ search​ engines​ in​ a descending order,​ with​ the​ details​ of​ visited​ pages​ as​ well​ last​ 30 days​ cumulative​ data​ with​ graph.</td>
	</tr>
	<tr>
		<td>22</td>
		<td>[<?php echo WSM_PREFIX ?>_showVisitorsDetail]</td>
		<td><b>Type​ : </b>​ Hourly,​ Daily,​ Monthly <br/><b>Condition​ : </b>​ Normal,​ Range,​ Compare<br/><b>From :​ </b>​ Start​ Date <br/><b>To :​</b> End​ Date <br/><b>First​ : </b>​ First​ date​ for​ comparison​ (Compare) <br/><b>Second​ : </b>​ second​ date​ for​ comparison (Compare) <br/><b>Example : </b><br/>[wsm_showVisitorsDetail type="Hourly" condition="Normal" from="2017-08-29" to="2017-08-29"]</td>
		<td>Shows​ a list​ of​ top​ Operating​ systems,​ Browsers​ and Screen​ resolutions​ based​ on​ selected​ date,​ or​ date range​ including​ last​ 30​ days​ cumulative​ data​ and graph. </td>
	</tr>
	<tr>
		<td>23</td>
		<td>[<?php echo WSM_PREFIX ?>_showVisitorsDetailGraph]</td>
		<td><b>Type​ : </b>​ Hourly,​ Daily,​ Monthly <br/><b>Condition​ : </b>​ Normal,​ Range,​ Compare<br/><b>From :​ </b>​ Start​ Date <br/><b>To :​</b> End​ Date <br/><b>First​ : </b>​ First​ date​ for​ comparison​ (Compare) <br/><b>Second​ : </b>​ second​ date​ for​ comparison (Compare) <br/><b>Example : </b><br/>[wsm_showVisitorsDetailGraph type="Hourly" condition="Normal" from="2017-08-30" to="2017-08-30"]</td>
		<td>It​ shows​ Operating​ System​ , Browser,​ and​ Screen resolutions​ wise​ statistics​ in​ pie​ chart​ based​ on selected​ date​ or​ date​ range. </td>
	</tr>
	<tr>
		<td>24</td>
		<td>[<?php echo WSM_PREFIX ?>_showStatKeywords]</td>
		<td>-</td>
		<td>Shows​ the​ list​ of​ searched​ keywords​ along​ with​ the search​ URL,​ date​ and​ ip​ address. </td>
	</tr>
	<tr>
		<td>25</td>
		<td>[<?php echo WSM_PREFIX ?>_showGeoLocationGraph]</td>
		<td><b>Type​ : </b>​ Hourly,​ Daily,​ Monthly <br/><b>Condition​ : </b>​ Normal,​ Range,​ Compare <br/><b>From :​ </b>​ Start​ Date <br/><b>To :​</b> End​ Date <br/><b>First​ : </b>​ First​ date​ for​ comparison​ (Compare) <br/><b>Second​ : </b>​ second​ date​ for​ comparison (Compare) <br/><b>Example : </b><br/>[wsm_showGeoLocationGraph type="Hourly" condition="Normal" from="2017-08-30" to="2017-08-30"]</td>
		<td>Shows​ the​ pie​ chart​ containing​ the​ statistics​ by​ country and​ city,​ based​ on​ selected​ date​ or​ date​ range. </td>
	</tr>
	<tr>
		<td>26</td>
		<td>[<?php echo WSM_PREFIX ?>_showGeoLocationDetails]</td>
		<td><b>Type​ : </b> Hourly,​ Daily,​ Monthly <br/><b>Condition​ : </b>​ Normal,​ Range,​ Compare <br/><b>From :​ </b>​ Start​ Date <br/><b>To :​</b> End​ Date <br/><b>First​ : </b>​ First​ date​ for​ comparison​ (Compare) <br/><b>Second​ : </b>​ second​ date​ for​ comparison (Compare) <br/><b>Example : </b><br/>[wsm_showGeoLocationDetails type="Hourly" condition="Normal" from="2017-08-28" to="2017-08-28"]</td>
		<td>Shows​ the​ list​ of​ countries​ in​ cities​ containing​ the statistics​ based​ on​ selected​ date​ or​ date​ range. </td>
	</tr>
	<tr>
		<td>27</td>
		<td>[<?php echo WSM_PREFIX ?>_showContentByURLStats]</td>
		<td><b>Type​ : </b>​ Hourly,​ Daily,​ Monthly <br/><b>Condition​ : </b>​ Normal,​ Range,​ Compare <br/><b>From :​ </b>​ Start​ Date <br/><b>To :​</b> End​ Date <br/><b>First​ : </b> First​ date​ for​ comparison​ (Compare) <br/><b>Second​ : </b>​ second​ date​ for​ comparison (Compare) <br/><b>Example : </b><br/>[wsm_showContentByURLStats type="Hourly" condition="Normal" from="2017-08-27" to="2017-08-27"]</td>
		<td>It​ shows​ the​ statistic​ by​ URL​ OR​ title​ including​ number of​ hits,​ visitor​ entries​ and​ new​ visitors​ entry​ based​ on selected​ date​ or​ date​ range. </td>
	</tr>
	<tr>
		<td>28</td>
		<td>[<?php echo WSM_PREFIX ?>_showTitleCloud]</td>
		<td><b>Type​ : </b>​ Hourly,​ Daily,​ Monthly <br/><b>Condition​ : </b>​ Normal,​ Range,​ Compare <br/><b>From :​ </b>​ Start​ Date <br/><b>To :​</b> End​ Date <br/><b>First​ : </b>​ First​ date​ for​ comparison​ (Compare) <br/><b>Second​ : </b>​ second​ date​ for​ comparison (Compare) <br/><b>Example : </b><br/>[wsm_showTitleCloud type="Hourly" condition="Normal" from="2017-08-29" to="2017-08-29"]</td>
		<td>Shows​ the​ title​ cloud​ based​ on​ selected​ date​ or​ date range. </td>
	</tr>
	<tr>
		<td>29</td>
		<td>[<?php echo WSM_PREFIX ?>_showGeneralStats]</td>
		<td>-</td>
		<td>Shows general stats in horizontal box.</td>
	</tr>
	<tr>
		<td>30</td>
		<td>[<?php echo WSM_PREFIX ?>_showEachVisitorsDetailGraph]</td>
		<td><b>Type​ : </b>​ Hourly,​ Daily,​ Monthly <br/><b>Condition​ : </b>​ Normal,​ Range,​ Compare<br/><b>From :​ </b>​ Start​ Date <br/><b>To :​</b> End​ Date <br/><b>First​ : </b>​ First​ date​ for​ comparison​ (Compare) <br/><b>Second​ : </b>​ second​ date​ for​ comparison (Compare) <br/><b>Example : </b><br/><b>display : </b>​ os, ​browser,​ resolution<br/>[wsm_showEachVisitorsDetailGraph display="os" type="Hourly" condition="Normal" from="2017-08-30" to="2017-08-30"]</td>
		<td>It​ shows​ Operating​ System​ or Browser or​ Screen resolutions​ wise​ statistics​ in​ pie​ chart​ based​ on selected​ display and date​ or​ date​ range. If you don't pass display then it will display for all 3 pie charts.</td>
	</tr>
</table>
