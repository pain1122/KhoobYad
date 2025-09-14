<?php

$user_id = $_GET['uid'];
$user = new user($user_id);
$username = $user->get_nick_name();
$grade = $user->get_grade();
$fos = $user->get_fos();
$school = $user->get_school();
$adviser = $user->get_adviser();
$phone_number = $user->get_user_meta('phonenumber');
$firstname = $user->get_user_meta('firstname');
$lastname = $user->get_user_meta('lastname');
$avatar = base::displayphoto($user->get_user_meta('avatar'));
$weeks_q = "SELECT `post_id` FROM `post` WHERE `post_type` = 'weekly_plan' AND `author` = $user_id";
$weeks = base::FetchArray($weeks_q);

if (isset($_GET['week_id'])) {
    $week_id = $_GET['week_id'];
    $week = new post($week_id);
    $week_title = $week->get_meta("week_title");
    $query = "SELECT `author`,`post_id`,`post_title` FROM `post`
    WHERE `post_parent` = $week_id
    AND `post_type` = 'daily-plan'
    GROUP BY `post`.`post_id`
    ORDER BY `post_id` ASC;";
    $all_days = base::FetchArray($query);
    $days = [];
    $all_days = array_reverse($all_days);
    foreach ($all_days as $day) :
        if ($day['post_id'] > 0) :
            $day_id = $day['post_id'];
            $user_id = $day['author'];
            unset($day['post_id']);
            unset($day['author']);
            $day_name = $day['post_title'];
            $days[$day_name] = [];
            $plans = base::FetchArray("SELECT `post_id` FROM `post` WHERE `post_type` = 'plan' AND `post_parent` = $day_id ORDER BY `post_title` ASC");
            foreach ($plans as $plan) {
                $plan_id = $plan['post_id'];
                if ($plan_id) {
                    $class_link_text = '';
                    unset($plan['post_id']);
                    $plan = new post($plan_id);
                    $text = $plan->get_content();
                    $start = $plan->get_title();
                    $end = $plan->get_excerpt();
                    $score = $plan->get_meta('score');
                    $time1 = strtotime($start);
                    $time2 = strtotime($end);
                    $difference = round(abs($time2 - $time1) / 3600, 2);
                    $comment = $plan->get_meta('comment');
                    for ($i = 1; $i < 4; $i++) {
                        $class_link = $plan->get_meta("class_link$i");
                        $class_link_title = $plan->get_meta("class_link_title$i");
                        if (!empty($class_link)) {
                            $class_link_text .= "<a target='_blank' class='btn btn-primary btn-xs mx-1' href='$class_link'>$class_link_title</a>";
                        }
                    }
                    if (!empty($score)) {
                        if ($score === '1')
                            $score = '#39da8a';
                        elseif ($score === '2')
                            $score = "#00cfdd";
                        elseif ($score === '3')
                            $score = "#fdac41";
                        elseif ($score === '4')
                            $score = "#ff5b5c";
                        else
                            $score = "#443453";
                    }
                    $info['text'] = $text;
                    $info['start'] = $start;
                    $info['end'] = $end;
                    $info['score'] = $score;
                    $info['difference'] = $difference;
                    $info['comment'] = $comment;
                    array_push($days[$day_name],$info);
                }
            }
        endif;
    endforeach;
}
?>
<link rel="stylesheet" href="assets/vendor/css/rtl/schedule.css">
<div class="cd-schedule loading">
    <div class="timeline">
        <ul>
            <li><span>00:00</span></li>
            <li><span>00:30</span></li>
            <li><span>01:00</span></li>
            <li><span>01:30</span></li>
            <li><span>02:00</span></li>
            <li><span>02:30</span></li>
            <li><span>03:00</span></li>
            <li><span>03:30</span></li>
            <li><span>04:00</span></li>
            <li><span>04:30</span></li>
            <li><span>05:00</span></li>
            <li><span>05:30</span></li>
            <li><span>06:00</span></li>
            <li><span>06:30</span></li>
            <li><span>06:00</span></li>
            <li><span>06:30</span></li>
            <li><span>07:00</span></li>
            <li><span>07:30</span></li>
            <li><span>08:00</span></li>
            <li><span>08:00</span></li>
            <li><span>09:30</span></li>
            <li><span>09:30</span></li>
            <li><span>10:00</span></li>
            <li><span>10:30</span></li>
            <li><span>11:00</span></li>
            <li><span>11:30</span></li>
            <li><span>12:00</span></li>
            <li><span>12:30</span></li>
            <li><span>13:00</span></li>
            <li><span>13:30</span></li>
            <li><span>14:00</span></li>
            <li><span>14:30</span></li>
            <li><span>15:00</span></li>
            <li><span>15:30</span></li>
            <li><span>16:00</span></li>
            <li><span>16:30</span></li>
            <li><span>17:00</span></li>
            <li><span>17:30</span></li>
            <li><span>18:00</span></li>
            <li><span>18:30</span></li>
            <li><span>19:00</span></li>
            <li><span>19:30</span></li>
            <li><span>20:00</span></li>
            <li><span>20:30</span></li>
            <li><span>21:00</span></li>
            <li><span>21:30</span></li>
            <li><span>22:00</span></li>
            <li><span>22:30</span></li>
            <li><span>23:00</span></li>
            <li><span>23:30</span></li>
            <li><span>24:00</span></li>
        </ul>
    </div> <!-- .timeline -->

    <div class="events">
        <ul class="wrap">
            <?php foreach ($days as $day => $plans) {
                $count_plans = 0; ?>
                <li class="events-group">
                    <div class="top-info"><span><?php echo $day; ?></span></div>
                    <ul>
                        <?php foreach ($plans as $plan) { ?>
                            <li class="single-event" data-start="<?php echo $plan['start']; ?>" data-end="<?php echo $plan['end']; ?>" data-content="<?php echo $plan['comment']; ?>" data-event="event-<?php echo $count_plans; ?>" data-background="<?php echo $score; ?>">
                                <a href="#0">
                                    <em class="event-name"><?php echo $plan['text']; ?></em>
                                </a>
                            </li>
                        <?php $count_plans++;
                        } ?>
                    </ul>
                </li>
            <?php } ?>
        </ul>
    </div>

    <div class="event-modal">
        <header class="header">
            <div class="content">
                <span class="event-date"></span>
                <h3 class="event-name"></h3>
            </div>

            <div class="header-bg"></div>
        </header>

        <div class="body">
            <div class="event-info"></div>
            <div class="body-bg"></div>
        </div>

        <a href="#0" class="close">Close</a>
    </div>

    <div class="cover-layer"></div>
</div>
<script>
    $('#layout-navbar').remove();
    $('#layout-menu').remove();
    $(".container-xxl.flex-grow-1.container-p-y").prependTo("body");
    $(".layout-wrapper.layout-content-navbar").remove();
    jQuery(document).ready(function($) {
        var transitionEnd = 'webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend';
        var transitionsSupported = ($('.csstransitions').length > 0);
        //if browser does not support transitions - use a different event to trigger them
        if (!transitionsSupported) transitionEnd = 'noTransition';

        //should add a loding while the events are organized 

        function SchedulePlan(element) {
            this.element = element;
            this.timeline = this.element.find('.timeline');
            this.timelineItems = this.timeline.find('li');
            this.timelineItemsNumber = this.timelineItems.length;
            this.timelineStart = getScheduleTimestamp(this.timelineItems.eq(0).text());
            //need to store delta (in our case half hour) timestamp
            this.timelineUnitDuration = getScheduleTimestamp(this.timelineItems.eq(1).text()) - getScheduleTimestamp(this.timelineItems.eq(0).text());

            this.eventsWrapper = this.element.find('.events');
            this.eventsGroup = this.eventsWrapper.find('.events-group');
            this.singleEvents = this.eventsGroup.find('.single-event');
            this.eventSlotHeight = this.eventsGroup.eq(0).children('.top-info').outerHeight();

            this.modal = this.element.find('.event-modal');
            this.modalHeader = this.modal.find('.header');
            this.modalHeaderBg = this.modal.find('.header-bg');
            this.modalBody = this.modal.find('.body');
            this.modalBodyBg = this.modal.find('.body-bg');
            this.modalMaxWidth = 800;
            this.modalMaxHeight = 480;

            this.animating = false;

            this.initSchedule();
        }

        SchedulePlan.prototype.initSchedule = function() {
            this.scheduleReset();
            this.initEvents();
        };

        SchedulePlan.prototype.scheduleReset = function() {
            var mq = this.mq();
            if (mq == 'desktop' && !this.element.hasClass('js-full')) {
                //in this case you are on a desktop version (first load or resize from mobile)
                this.eventSlotHeight = this.eventsGroup.eq(0).children('.top-info').outerHeight();
                this.element.addClass('js-full');
                this.placeEvents();
                this.element.hasClass('modal-is-open') && this.checkEventModal();
            } else if (mq == 'mobile' && this.element.hasClass('js-full')) {
                //in this case you are on a mobile version (first load or resize from desktop)
                this.element.removeClass('js-full loading');
                this.eventsGroup.children('ul').add(this.singleEvents).removeAttr('style');
                this.eventsWrapper.children('.grid-line').remove();
                this.element.hasClass('modal-is-open') && this.checkEventModal();
            } else if (mq == 'desktop' && this.element.hasClass('modal-is-open')) {
                //on a mobile version with modal open - need to resize/move modal window
                this.checkEventModal('desktop');
                this.element.removeClass('loading');
            } else {
                this.element.removeClass('loading');
            }
        };

        SchedulePlan.prototype.initEvents = function() {
            var self = this;

            this.singleEvents.each(function() {
                //create the .event-date element for each event
                var background = $(this).data('background');
                var durationLabel = '<span class="event-date">' + $(this).data('start') + ' - ' + $(this).data('end') + '</span>';
                $(this).children('a').prepend($(durationLabel));
                $(this).css("background-color", background);

            });

        };

        SchedulePlan.prototype.placeEvents = function() {
            var self = this;
            this.singleEvents.each(function() {
                //place each event in the grid -> need to set top position and height
                var start = getScheduleTimestamp($(this).attr('data-start')),
                    duration = getScheduleTimestamp($(this).attr('data-end')) - start;

                var eventTop = self.eventSlotHeight * (start - self.timelineStart) / self.timelineUnitDuration,
                    eventHeight = self.eventSlotHeight * duration / self.timelineUnitDuration;

                $(this).css({
                    top: (eventTop + 100 - 1) + 'px',
                    height: (eventHeight + 1) + 'px'
                });
            });

            this.element.removeClass('loading');
        };


        SchedulePlan.prototype.mq = function() {
            //get MQ value ('desktop' or 'mobile') 
            var self = this;
            return window.getComputedStyle(this.element.get(0), '::before').getPropertyValue('content').replace(/["']/g, '');
        };

        SchedulePlan.prototype.checkEventModal = function(device) {
            this.animating = true;
            var self = this;
            var mq = this.mq();

            if (mq == 'mobile') {
                //reset modal style on mobile
                self.modal.add(self.modalHeader).add(self.modalHeaderBg).add(self.modalBody).add(self.modalBodyBg).attr('style', '');
                self.modal.removeClass('no-transition');
                self.animating = false;
            } else if (mq == 'desktop' && self.element.hasClass('modal-is-open')) {
                self.modal.addClass('no-transition');
                self.element.addClass('animation-completed');
                var event = self.eventsGroup.find('.selected-event');

                var eventTop = event.offset().top - $(window).scrollTop(),
                    eventLeft = event.offset().left,
                    eventHeight = event.innerHeight(),
                    eventWidth = event.innerWidth();

                var windowWidth = $(window).width(),
                    windowHeight = $(window).height();

                var modalWidth = (windowWidth * .8 > self.modalMaxWidth) ? self.modalMaxWidth : windowWidth * .8,
                    modalHeight = (windowHeight * .8 > self.modalMaxHeight) ? self.modalMaxHeight : windowHeight * .8;

                var HeaderBgScaleY = modalHeight / eventHeight,
                    BodyBgScaleX = (modalWidth - eventWidth);

                setTimeout(function() {
                    self.modal.css({
                        width: modalWidth + 'px',
                        height: modalHeight + 'px',
                        top: (windowHeight / 2 - modalHeight / 2) + 'px',
                        left: (windowWidth / 2 - modalWidth / 2) + 'px',
                    });
                    transformElement(self.modal, 'translateY(0) translateX(0)');
                    //change modal modalBodyBg height/width
                    self.modalBodyBg.css({
                        height: modalHeight + 'px',
                        width: '1px',
                    });
                    transformElement(self.modalBodyBg, 'scaleX(' + BodyBgScaleX + ')');
                    //set modalHeader width
                    self.modalHeader.css({
                        width: eventWidth + 'px',
                    });
                    //set modalBody left margin
                    self.modalBody.css({
                        marginLeft: eventWidth + 'px',
                    });
                    //change modal modalHeaderBg height/width and scale it
                    self.modalHeaderBg.css({
                        height: eventHeight + 'px',
                        width: eventWidth + 'px',
                    });
                    transformElement(self.modalHeaderBg, 'scaleY(' + HeaderBgScaleY + ')');
                }, 10);

                setTimeout(function() {
                    self.modal.removeClass('no-transition');
                    self.animating = false;
                }, 20);
            }
        };

        var schedules = $('.cd-schedule');
        var objSchedulesPlan = [],
            windowResize = false;

        if (schedules.length > 0) {
            schedules.each(function() {
                //create SchedulePlan objects
                objSchedulesPlan.push(new SchedulePlan($(this)));
            });
        }

        $(window).on('resize', function() {
            if (!windowResize) {
                windowResize = true;
                (!window.requestAnimationFrame) ? setTimeout(checkResize): window.requestAnimationFrame(checkResize);
            }
        });

        $(window).keyup(function(event) {
            if (event.keyCode == 27) {
                objSchedulesPlan.forEach(function(element) {
                    element.closeModal(element.eventsGroup.find('.selected-event'));
                });
            }
        });

        function checkResize() {
            objSchedulesPlan.forEach(function(element) {
                element.scheduleReset();
            });
            windowResize = false;
        }

        function getScheduleTimestamp(time) {
            //accepts hh:mm format - convert hh:mm to timestamp
            time = time.replace(/ /g, '');
            var timeArray = time.split(':');
            var timeStamp = parseInt(timeArray[0]) * 60 + parseInt(timeArray[1]);
            return timeStamp;
        }

        function transformElement(element, value) {
            element.css({
                '-moz-transform': value,
                '-webkit-transform': value,
                '-ms-transform': value,
                '-o-transform': value,
                'transform': value
            });
        }
    });
    
    $(document).ready(function(){
        window.print();
        window.onafterprint = function() {
            window.history.back();
        };
    })
    


</script>