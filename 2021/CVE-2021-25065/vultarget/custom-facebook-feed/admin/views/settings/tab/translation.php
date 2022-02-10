<div v-if="selected === 'app-3'">
    <div class="sb-tab-box sb-custom-text-box d-flex">
        <div class="tab-label tab-label-full">
            <h3>{{translationTab.title}}</h3>
            <span class="sb-help-text">{{translationTab.description}}</span>

            <div class="sb-tab-inner-card">
                <table class="cff-table">
                    <thead>
                        <tr>
                            <th>{{translationTab.table.originalText}}</th>
                            <th>{{translationTab.table.customText}}</th>
                            <th>{{translationTab.table.context}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="cff-table-row-header">
                            <td colspan="3">{{translationTab.table.postText}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.seeMore}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_see_more_text" v-model:value="model.translation.cff_see_more_text"></td>
                            <td>{{translationTab.table.usedWhen}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.seeLess}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_see_less_text" v-model:value="model.translation.cff_see_less_text"></td>
                            <td>{{translationTab.table.usedWhen}}</td>
                        </tr>
                    </tbody>
                    <tbody v-if="licenseType == 'pro'">
                        <tr class="cff-table-row-header">
                            <td colspan="3">{{translationTab.table.events}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.map}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_map_text" v-model:value="model.translation.cff_map_text"></td>
                            <td>{{translationTab.table.addedAfter}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.noUpcoming}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_no_events_text" v-model:value="model.translation.cff_no_events_text"></td>
                            <td>{{translationTab.table.shownWhen}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.interested}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_interested_text" v-model:value="model.translation.cff_interested_text"></td>
                            <td>{{translationTab.table.usedFor}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.going}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_going_text" v-model:value="model.translation.cff_going_text"></td>
                            <td>{{translationTab.table.usedFor2}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.buyTickets}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_buy_tickets_text" v-model:value="model.translation.cff_buy_tickets_text"></td>
                            <td>{{translationTab.table.shownWhen2}}</td>
                        </tr>
                    </tbody>
                    <tbody>
                        <tr class="cff-table-row-header">
                            <td colspan="3">{{translationTab.table.postAction}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.viewOnFB}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_facebook_link_text" v-model:value="model.translation.cff_facebook_link_text"></td>
                            <td>{{translationTab.table.usedFor3}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.share}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_facebook_share_text" v-model:value="model.translation.cff_facebook_share_text"></td>
                            <td>{{translationTab.table.usedFor4}}</td>
                        </tr>
                    </tbody>
                    <tbody v-if="licenseType == 'pro'">
                        <tr class="cff-table-row-header">
                            <td colspan="3">{{translationTab.table.loadMoreBtn}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.loadMore}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_load_more_text" v-model:value="model.translation.cff_load_more_text"></td>
                            <td>{{translationTab.table.usedIn}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.noMorePosts}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_no_more_posts_text" v-model:value="model.translation.cff_no_more_posts_text"></td>
                            <td>{{translationTab.table.usedWhen2}}</td>
                        </tr>
                    </tbody>
                    <tbody v-if="licenseType == 'pro'">
                        <tr class="cff-table-row-header">
                            <td colspan="3">{{translationTab.table.likeShareComment}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.viewMore}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_view_previous_comments_text" v-model:value="model.translation.cff_translate_view_previous_comments_text"></td>
                            <td>{{translationTab.table.usedIn2}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.commentOnFB}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_comment_on_facebook_text" v-model:value="model.translation.cff_translate_comment_on_facebook_text"></td>
                            <td>{{translationTab.table.usedAt}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.photos}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_photos_text" v-model:value="model.translation.cff_translate_photos_text"></td>
                            <td>{{translationTab.table.addedTo}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.likeThis}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_like_this_text" v-model:value="model.translation.cff_translate_like_this_text"></td>
                            <td>{{translationTab.table.egLikeThis}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.likesThis}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_likes_this_text" v-model:value="model.translation.cff_translate_likes_this_text"></td>
                            <td>{{translationTab.table.egLikesThis}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.reactedToThis}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_reacted_text" v-model:value="model.translation.cff_translate_reacted_text"></td>
                            <td>{{translationTab.table.egReactedToThis}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.and}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_and_text" v-model:value="model.translation.cff_translate_and_text"></td>
                            <td>{{translationTab.table.egLikeThis}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.other}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_other_text" v-model:value="model.translation.cff_translate_other_text"></td>
                            <td>{{translationTab.table.eg1otherLike}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.others}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_others_text" v-model:value="model.translation.cff_translate_others_text"></td>
                            <td>{{translationTab.table.eg10othersLike}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.reply}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_reply_text" v-model:value="model.translation.cff_translate_reply_text"></td>
                            <td>{{translationTab.table.eg1reply}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.replies}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_replies_text" v-model:value="model.translation.cff_translate_replies_text"></td>
                            <td>{{translationTab.table.eg5replies}}</td>
                        </tr>
                    </tbody>
                    <tbody v-if="licenseType == 'free'">
                        <tr class="cff-table-row-header">
                            <td colspan="3">{{translationTab.table.media}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.photo}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_photo_text" v-model:value="model.translation.cff_translate_photo_text"></td>
                            <td>{{translationTab.table.usedTo1}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.video}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_video_text" v-model:value="model.translation.cff_translate_video_text"></td>
                            <td>{{translationTab.table.usedTo2}}</td>
                        </tr>
                    </tbody>
                    <tbody>
                        <tr class="cff-table-row-header">
                            <td colspan="3">{{translationTab.table.callToBTN}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.learnMore}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_learn_more_text" v-model:value="model.translation.cff_translate_learn_more_text"></td>
                            <td>{{translationTab.table.usedFor5}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.shopNow}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_shop_now_text" v-model:value="model.translation.cff_translate_shop_now_text"></td>
                            <td>{{translationTab.table.usedFor6}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.messagePage}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_message_page_text" v-model:value="model.translation.cff_translate_message_page_text"></td>
                            <td>{{translationTab.table.usedFor7}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.getDirections}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_get_directions_text" v-model:value="model.translation.cff_translate_get_directions_text"></td>
                            <td>{{translationTab.table.usedFor8}}</td>
                        </tr>
                    </tbody>
                    <tbody>
                        <tr class="cff-table-row-header">
                            <td colspan="3">{{translationTab.table.date}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.second}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_second" v-model:value="model.translation.cff_translate_second"></td>
                            <td>{{translationTab.table.usedFor9}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.seconds}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_seconds" v-model:value="model.translation.cff_translate_seconds"></td>
                            <td>{{translationTab.table.usedFor10}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.minute}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_minute" v-model:value="model.translation.cff_translate_minute"></td>
                            <td>{{translationTab.table.usedFor11}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.minutes}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_minutes" v-model:value="model.translation.cff_translate_minutes"></td>
                            <td>{{translationTab.table.usedFor12}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.hour}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_hour" v-model:value="model.translation.cff_translate_hour"></td>
                            <td>{{translationTab.table.usedFor13}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.hours}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_hours" v-model:value="model.translation.cff_translate_hours"></td>
                            <td>{{translationTab.table.usedFor14}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.day}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_day" v-model:value="model.translation.cff_translate_day"></td>
                            <td>{{translationTab.table.usedFor15}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.days}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_days" v-model:value="model.translation.cff_translate_days"></td>
                            <td>{{translationTab.table.usedFor16}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.week}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_week" v-model:value="model.translation.cff_translate_week"></td>
                            <td>{{translationTab.table.usedFor17}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.weeks}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_weeks" v-model:value="model.translation.cff_translate_weeks"></td>
                            <td>{{translationTab.table.usedFor18}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.month}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_month" v-model:value="model.translation.cff_translate_month"></td>
                            <td>{{translationTab.table.usedFor19}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.months}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_months" v-model:value="model.translation.cff_translate_months"></td>
                            <td>{{translationTab.table.usedFor20}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.year}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_year" v-model:value="model.translation.cff_translate_year"></td>
                            <td>{{translationTab.table.usedFor21}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.years}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_years" v-model:value="model.translation.cff_translate_years"></td>
                            <td>{{translationTab.table.usedFor22}}</td>
                        </tr>
                        <tr>
                            <td>{{translationTab.table.ago}}</td>
                            <td><input type="text" class="cff-input" :placeholder="model.translation.cff_translate_ago" v-model:value="model.translation.cff_translate_ago"></td>
                            <td>{{translationTab.table.usedFor23}}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>{{translationTab.table.originalText}}</th>
                            <th>{{translationTab.table.customText}}</th>
                            <th>{{translationTab.table.context}}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- todo: this is just demo content and will be replaced once I work on this -->