<?php 
    include_once('config.php');
    
    $wall_id = $_GET['wall_id']?$_GET['wall_id']:false;
    $boulder_id = $_GET['boulder_id']?$_GET['boulder_id']:false;
?>

<!DOCTYPE html>

<html>
	<head>
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
        
        <title>BamBoulder</title>
        <meta name="description" content="Bambergs schönster Boulderschuppen glänzt mit neuen Griffen, hier erfahrt ihr was andere gebouldert sind. Messt euch mit den Besten." />
		<meta NAME="ROBOTS" CONTENT="INDEX, FOLLOW" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE" />
        <meta name="author" content="Christian Matyas">
        <meta name="DC.contributor" content="Christian Matyas">
        <meta name="keywords" content="Bouldern, Bamberg, Gaustadt, DAV">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" />
	    <link rel="stylesheet" href="css/themes/boulder_blue.min.css" />
        <link rel="stylesheet" href="css/jquery.mobile.structure-1.2.0.min.css" />

		
        
        <style type="text/css" media="all">@import "css/annotation.css";</style>
        <style type="text/css" media="all">@import "css/style.css";</style>

		<script type="text/javascript" src="js/jquery-1.8.3.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.9.2.custom.js"></script>
		<!--<script type="text/javascript" src="js/jquery.annotate.js"></script>-->
		<script type="text/javascript" src="js/knockout-2.2.0.js"></script>
        <script type="text/javascript" src="js/knockout.mapping-latest.js"></script>
        <script type="text/javascript" src="js/viewmodel/BoulderViewModel.js" ></script>
        <script type="text/javascript" src="js/jquery.mobile-1.2.0.min.js"></script>
        <?php 
            if(!_LOCAL){
        ?>        
            <script type="text/javascript">

              var _gaq = _gaq || [];
              _gaq.push(['_setAccount', 'UA-37484206-1']);
              _gaq.push(['_trackPageview']);

              (function() {
                var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
              })();

            </script>
        <?php 
            }
        ?>
	</head>
	<body> 

    <div id="fb-root"></div>
    <script>
        //FACEBOOK
        // Additional JS functions here
        window.fbAsyncInit = function () {
            FB.init({
                appId: '<?php echo _FACEBOOK_APPID?>', // App ID
                channelUrl: '<?php echo _FACEBOOK_CHANNEL_URL?>', // Channel File
                status: true, // check login status
                cookie: true, // enable cookies to allow the server to access the session
                xfbml: true  // parse XFBML
            });

            // Additional init code here
            FB.getLoginStatus(function (response) {
                if (response.status === 'connected') {
                    viewmodel.loginFacebook();
                } else if (response.status === 'not_authorized') {
                    viewmodel.LoadingUser(false);
                } else {
                    viewmodel.LoadingUser(false);
                }
            });
        };

        // Load the SDK Asynchronously
        (function (d) {
            var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
            if (d.getElementById(id)) { return; }
            js = d.createElement('script'); js.id = id; js.async = true;
            js.src = "//connect.facebook.net/en_US/all.js";
            ref.parentNode.insertBefore(js, ref);
        } (document));
    </script>

    <!-- Direct Boulder Load -->
    <script>
    $(window).ready(function () {
        <?php 
        if($wall_id && $boulder_id){
        ?>
            viewmodel = new BoulderViewModel('<?php echo _FACEBOOK_APPID?>',<?php echo $wall_id ?>,<?php echo $boulder_id ?>);
        <?php 
        }else{
        ?>
            viewmodel = new BoulderViewModel('<?php echo _FACEBOOK_APPID?>');
        <?php 
        }
        ?>
        ko.applyBindings(viewmodel);
    });
    </script>
    
    <div data-bind="visible: Loading" class="canvas_loading" style="width: 100%; height: 100%; background-color: #FFF">
        <img src="images/loading.gif" />
    </div>
    
    <div data-role="page" data-bind="visible: !Loading()">
        <!-- ko ifnot: userLoggedin() || printMode() -->
                <a href="#popup_login" data-rel="popup" data-inline="true" data-mini="false" data-role="button" data-position-to="window" data-transition="fade" style="position: absolute; z-index:1000; top: 0px; left: 5px; cursor: pointer">Anmelden</a>
        <!-- /ko -->
	    <div data-role="header" class="canvas_header">        
            <!-- ko if: userLoggedin() && !printMode() -->
                    <a data-bind="click: changeToProfileMode" data-icon="profile" data-role="button" data-iconpos="notext" data-bind="visible: !printMode()">Profile</a>
            <!-- /ko -->
            <h1>
                <a data-role="button" data-mini="true" data-icon="arrow-l" data-iconpos="notext" data-inline="true" data-bind="click: previousBoulder, visible: boulders() != null && boulders().length > 0 && boulders().indexOf(currentBoulder()) > 0" style="top: 0px;">zurück</a>
		        <span data-bind="text: currentBoulderName"></span>
                <a data-role="button" data-mini="true" data-icon="arrow-r" data-iconpos="notext" data-inline="true" data-bind="click: nextBoulder, visible: boulders() != null && boulders().length > 0 && boulders().indexOf(currentBoulder()) < boulders().length-1" style="top: 0px;">nächster</a>   
            </h1>
            
            <a data-bind="click: changeToNewsMode" data-icon="info" data-role="button" data-iconpos="notext" data-bind="visible: !printMode()" title="Erfahrt was neues passiert ist.">Neuighkeiten</a>
            
	    </div><!-- /header -->
        <div data-role="navbar" data-theme="b" data-bind="visible: !printMode()">
            <ul>
		        <li><a class="ui-btn-active ui-state-persist" data-bind="click: changeToSelectionMode" data-icon="home">Auswahl</a></li>
                <li><a data-bind="click: changeToHighscoreMode" data-icon="star">Highscore</a></li>
                <li><a data-bind="click: changeToGridMode" data-icon="grid">Übersicht</a></li>
            </ul>
        </div>
        <!-- /navbar -->
        
        <!--
	    <div class="news" data-bind="visible: !printMode()" >
            <b>Neuigkeiten:</b> 
                <div style="float:right" class="annotation link" onclick="$('.news').slideUp()">close</div>
                <ul>
                    <li >20.02.2013 - Alle <a class="link" data-bind="click: changeToNewsMode">Neuigkeiten</a> nun auf einen Blick: Info-Knopf oben rechts.</li>
                    <li style="visibility: hidden; display: none">22.01.2013 - Es darf mit diskutiert werden im neuen  <a href="http://board.rocknclimb.de/viewforum.php?f=3" target="_blank">Forum</a>.</li>
                    <li style="visibility: hidden; display: none">27.01.2013 - Die ersten Awards sind online, schau gleich mal in dein <a class="link" data-bind="click: changeToProfileMode">Profil</a>, wie viele du bereits geschafft hast.</li>
                    <li style="visibility: hidden; display: none">29.01.2013 - In der <a class="link" data-bind="click: changeToGridMode">Übersicht</a> werden alle beendeten Boulder grün markiert. Bewertungen haben nun eine zusätzliche Staffelung</li>
                    <li >02.02.2013 - Es wurde ein Fehler behoben, der das anonyme Nutzer daran gehindert hat, eigene Boulder einzutragen.</li> 
                </ul>
        </div>-->
        <div data-role="content">
        
            <div data-bind="visible: selectionMode() && !printMode()">
                <div class="ui-grid-a">
	                <div class="ui-block-a">
                        <select id="selection_walls" data-bind="foreach: walls, attr: {value: currentWall()?currentWall().Id:''}" onchange="viewmodel.changeWall(this.value)"  data-mini="true">
                            <option data-bind="text: Name, value:Id, attr: {selected: $root.currentWall()?$root.currentWall().Id == Id:false}"></option>
                        </select>
                    </div>
                    <div class="ui-block-b">
                        <select id="selection_boulders" data-bind="foreach: boulders, attr: {value: currentBoulder()?currentBoulder().Id:''}" onchange="viewmodel.changeBoulder(this.value)"  data-mini="true">
                            <option data-bind="text: BoulderName, value:Id, attr: {selected: $root.currentBoulder()?$root.currentBoulder().Id == Id:false}, css: { 
                                        boulder_diff_0: Math.ceil(Difficulty()) == 0,
                                        boulder_diff_1: Math.ceil(Difficulty()) == 1,
                                        boulder_diff_2: Math.ceil(Difficulty()) == 2,
                                        boulder_diff_3: Math.ceil(Difficulty()) == 3,
                                        boulder_diff_4: Math.ceil(Difficulty()) == 4,
                                        boulder_diff_5: Math.ceil(Difficulty()) == 5
                                    }"></option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div data-bind="visible: editMode">
                    <!-- ko with: currentBoulder -->
                    <div class="canvas_edit">
                        <table width="100%" cellspacing="0">
                            <tr>
	                            <td style="width: 120px;">
                                    <b>Name:</b>
                                </td>                       
                                <td>
                                    <input type="text" data-bind="value: Name" data-mini="true"/>
                                </td>
                                <td rowspan="4" width="40px" style="padding-left: 20px; font-size: 10pt;">
                                    <a href="#popup_info" data-rel="popup" data-role="button" data-icon="info" data-iconpos="notext" data-position-to="window"  data-transition="fade">Anleitung</a>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 120px;">
                                    <b>Beschreibung:</b>
                                </td>                       
                                <td>
                                    <input type="text" data-bind="value: Description" data-mini="true"/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Remix: <input type="checkbox" data-bind="checked: IsRemix" data-role="none"/>
                                </td>
                                <td>
                                    Spax: <input type="checkbox" data-bind="checked: StepSpax" data-role="none"/>
                                    Grip is step: <input type="checkbox" data-bind="checked: StepGrip" data-role="none"/>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="button" data-mini="true" data-bind="click: save" value="Save Boulder"/>
                                    <a data-bind="click: $root.deleteBoulder, clickBubble: false" class="button_delete">delete</a>
                                </td>
                            </tr>        
                        </table>  
                    </div>
                    <!-- /ko -->
            </div>
            
            <!-- IMAGE CANVAS -->
            <div data-bind="visible:  !profileMode() && !highscoreMode() && !gridMode() && !newsMode()">
                <div id="canvas_image" class="canvas">
                    <div class="image-annotate-canvas shadow" data-bind="style: {backgroundImage: currentWallImage}, click: imageClicked" style="width:100%; height:100%; margin-left: auto; margin-right: auto">
                        <div class="image-annotate-view">   
                        <!-- ko if: userLoggedin() && !printMode() -->
                        <div data-bind="css: {boulder_finished: currentBoulder()?currentBoulder().Completed():false, boulder_unfinished: currentBoulder()?!currentBoulder().Completed():true },click: completeBoulder" id="boulder_status" title="Boulder finished!">
                        </div>
                        <div id="boulder_facebook" title="Post to Facebook.">
                            <a data-bind="click: publishToFacebook">
                                <img src="images/facebook_small.png" width="48" height="48" />
                            </a>
                        </div>
                        <!-- /ko -->
                        <select data-bind="value: scale" data-mini="true" data-icon="gear" id="scale_selection" data-inline="true">
                            <option value="1">100%</option>
                            <option value="0.9">90%</option>
                            <option value="0.8">80%</option>
                            <option value="0.7">70%</option>
                            <option value="0.6">60%</option>
                            <option value="0.5">50%</option>
                            <option value="0.4">40%</option>
                            <option value="0.3">30%</option>
                            <option value="0.2">20%</option>
                        </select>
                        <!-- ko ifnot: editMode() || printMode() -->
                            <!-- ko foreach: boulders -->
                                <!-- ko foreach: Nodes -->
                                    <div data-bind="style: { top: top, left: left, width: width, height: height, border: '1px solid '+randomColor()}, attr:{class: 'node_boulder_'+boulder(), title: $parent.Name}, click: function(data, event) { viewmodel.changeBoulder($parent.Id())}, event: { mouseover: $root.highlightBoulder, mouseout: $root.unhighlightBoulder } " style="position: absolute;opacity:0.3;filter:alpha(opacity=40);border-radius: 5px;">
                                    </div>
                                <!-- /ko -->
                            <!-- /ko -->
                        <!-- /ko -->
                        
                        <!-- ko foreach: nodes -->
                            <div class="image-annotate-edit-area" data-bind="style: { top: top, left: left, width: width, height: height}, click: $root.editNode, clickBubble: false, attr:{id: 'node_'+id()} " style="position: absolute"></div>
                                <div data-bind="style: { top: textTop, left: textLeft }, css: {'image-annotate-edit-form': editMode}" style="position: absolute">
                                    <form data-bind="visible: editMode">
                                        <textarea id="image-annotate-text" name="text" rows="1" cols="20" data-bind="value: text" data-role="none"></textarea>
                                        <!--<select data-bind="value: type">-->
                                            <!-- ko foreach: $root.nodetypes -->
                                                    <!--<option data-bind="value: id, text: name"></option>-->
                                            <!-- /ko -->
                                        <!--</select>-->
                                        <div>
                                            <input type="button" data-bind="click: save, clickBubble: false" value="ok" data-role="none"/> 
                                            <!--<input type="button" data-bind="click: cancel" value="cancel"/>-->
                                            <a data-bind="click: $root.removeNode, clickBubble: false" class="button_delete">delete</a>
                                        </div>
                                    </form>
                            
                                    <div data-bind="text: text, visible: !editMode()"></div>
                                </div>
                        <!-- /ko -->
                        
                        </div>
                        <div class="image-annotate-edit">
                            <div class="image-annotate-edit-area"></div>
                        </div>
                        <div data-bind="visible: !$root.printMode()" style="position: absolute; bottom: 5px; left: 5px;">
                            <div data-role="button" data-mini="true" data-inline="true" data-bind="click: $root.changeWallLeft, visible: $root.currentWall()?($root.currentWall().Left > 0):false" >Nach Links</div>
                        </div>
                        <div data-bind="visible: !$root.printMode()" style="position: absolute; bottom: 5px; right: 5px;">
                            <div data-role="button" data-mini="true" data-inline="true" data-bind="click: $root.changeWallRight, visible: $root.currentWall()?($root.currentWall().Right > 0):false">Nach Rechts</div>
                        </div>
                        
                    </div>

                </div>
                    
                    <div id="canvas_wrapper" class="canvas">
                    <!-- ko with: currentBoulder -->
                        <div id="canvas_boulder_comments">
                            <h3 style="margin-top:0px;">Bewertungen:</h3>
                            <div class="canvas_comments_averages">
                                <div>Durchschnittlicher Schwierigkeit: <span data-bind="text: Difficulty"></span></div>                  
                                <div>Durchschnittlicher Spassfaktor: <span data-bind="text: Rating"></span></div>
                            </div>
                            <div data-bind="foreach: Comments">
                                <div class="canvas_comment">
                            
                                    <div style="float:right">
                                        <p data-bind="visible: $root.user()?$root.user().id == UserId():false">
                                            <a data-role="button" data-inline="true" data-mini="true" data-icon="delete" data-bind="click: $root.deleteComment" data-iconpos="notext">löschen</a>
                                        </p>
                                        <!-- ko if: FacebookId() == 0 -->
                                            <!-- ko if: UserName() == "" -->
                                                <span class="annotation">anonym</span> 
                                            <!-- /ko -->
                                            <!-- ko ifnot: UserName() == "" --> 
                                                <span data-bind="text: UserName"></span>
                                            <!-- /ko -->
                                        <!-- /ko -->
                                        <!-- ko if: FacebookId() > 0 -->
                                            <fb:profile-pic data-bind="attr: {uid: FacebookId}" facebook-logo="true" linked="false" size="square" width="30px" height="30px"></fb:profile-pic>
                                        <!-- /ko -->
                                
                                        
                                    </div>
                                    <div class="comment_text">
                                
                                        <div data-bind="text: Comment"></div>
                                        <div data-bind="visible: Difficulty() > 0 || Rating() > 0">
                                            <b>Schwierigkeit:</b> <span data-bind="text: Difficulty"></span> /
                                            <b>Spassfaktor:</b> <span data-bind="text: Rating"></span> 
                                        </div>
                                    </div>
                            
                                </div>
                            </div>
                            <div onclick="$(this).next().slideToggle();" data-role="button" data-mini="true" data-icon="plus">Neuer Kommentar</div>
                            <div style="display: none;">
                                <table class="canvas_new_comment">
                                    <tr>
                                        <td colspan="2">
                                            <textarea data-mini="true" data-bind="value: $root.newcomment"></textarea>
                                        </td>
                                    </tr>
                                    <tr data-bind="visible: $root.currentBoulder().Completed()">
                                        <td width="200px;">
                                            <div class="annotation">Spass:</div>
                                            <select data-mini="true" data-bind="value: $root.newcomment_rating" data-role="none">
                                                <option value="0">keine Bewertung</option>
                                                <option value="1">1 - geht so</option>
                                                <option value="2">2 - ok</option>
                                                <option value="3">3 - perfekt</option>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="annotation">Schwierigkeit:</div>
                                            <select data-mini="true"  data-bind="value: $root.newcomment_difficulty"  data-role="none">
                                                <option value="0">keine Bewertung</option>
                                                <option value="1" class="main_diff">1 - Anfänger</option>
                                                <option value="1.5">1,5</option>
                                                <option value="2" class="main_diff">2 - Hobbyboulderer</option>
                                                <option value="2.5">2,5</option>
                                                <option value="3" class="main_diff">3 - Fortgeschrittener</option>
                                                <option value="3.5">3,5</option>
                                                <option value="4" class="main_diff">4 - Profi</option>
                                                <option value="4.5">4,5</option>
                                                <option value="5" class="main_diff">5 - Weltmeister</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <div data-bind="visible: $root.newcomment_difficulty() > 0 || $root.newcomment_rating() > 0">
                                                <input type="button" value="Bewertung absenden" data-mini="true" data-bind="click: $root.addComment"/>
                                            </div>
                                            <div data-bind="visible: $root.newcomment_difficulty() == 0 && $root.newcomment_rating() == 0">
                                                <input type="button" value="Kommentar abgeben" data-mini="true" data-bind="click: $root.addComment"/>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div><!-- /canvas_boulder_comments -->
                        
                        <!-- /ko -->
                        <div id="canvas_boulder_description">
                            <!-- ko with: currentBoulder -->
                            <h3 style="margin-top:0px;">Beschreibung:</h3>
                            <div data-bind="text: Description"></div>
                            <div style="margin-top: 10px; padding:0px" class="annotation">
                                <b>URL:</b>
                                <span data-bind="text: '<?php echo(_FACEBOOK_CHANNEL_URL); ?>?wall_id='+WallId()+'&boulder_id='+Id()" />
                            </div>
                    
                            <h3>Tritte:</h3>
                            <ul>
                                <li data-bind="visible: StepSpax">Nur Spaxe</li>
                                <li data-bind="visible: StepGrip">Griff ist Tritt</li>
                            </ul>
                    
                            <h3  data-bind="visible: !$root.printMode()">Erstellt am:</h3>
                            <div data-bind="visible: !$root.printMode()">
                                <span data-bind="text: CreationDate"></span>
                                <span data-bind="visible: IsNew">NEU!</span>
                                <div data-bind="visible: IsRemix" class="annotation">(Remix erstellt von: <span data-bind="text: UserName"></span>)</div>
                            </div>
                    
                            <!-- /ko -->
                    
                            <h3 data-bind="visible: !printMode()">Finishers:</h3>
                            <div class="canvas_finishers" data-bind="foreach: finishers, visible: !printMode()">
                                <fb:profile-pic data-bind="attr: {uid: facebook_id}" facebook-logo="true" linked="false" size="square"></fb:profile-pic>      
                            </div>
                        </div><!-- /canvas_boulder_description -->
                    
                    
                        
                    </div><!-- /canvas_wrapper -->
            </div>
            <a type="button" data-bind="click: $root.changeToEditMode, visible: $root.CanEditBoulder" data-icon="gear">Bearbeiten</a>
        </div> <!-- /VIEW BOULDER -->
        
        <!-- VIEW PROFILE -->
        <div data-bind="visible: profileMode" class="canvas_profile">
            <div data-bind="with: user">
                <h2>Account</h2>
                <div>
                    <div data-bind="visible: facebook_id > 0">
                        <fb:profile-pic data-bind="attr: {uid: facebook_id}" facebook-logo="true" linked="false"></fb:profile-pic>
                    </div>
                    <span data-bind="text: username"></span>
                    (ID: <span data-bind="text: id"></span>)
                </div>
                <h2>Awards</h2>
                <!-- ko foreach: $root.highscore -->
                    <!-- ko if: user_id == $root.user().id -->
                        <!-- ko if: rank == 1 -->
                            <img src="images/ape_1.png" alt="Erster Platz!" title="Erster Platz in der Highscore." class="award"/>
                        <!-- /ko -->
                        <!-- ko if: rank == 2 -->
                            <img src="images/ape_2.png" alt="Zweiter Platz!" title="Zweiter Platz in der Highscore." class="award"/>
                        <!-- /ko -->
                        <!-- ko if: rank == 3 -->
                            <img src="images/ape_3.png" alt="Dritter Platz!" title="Dritter Platz in der Highscore." class="award"/>
                        <!-- /ko -->
                        <!-- ko if: rank > 3 -->
                            <img src="images/ape_grey_1.png" alt="Erster Platz!" title="Werde erster in der Highscore." class="award award_failed"/>
                            <img src="images/ape_grey_2.png" alt="Erster Platz!" title="Werde zweiter  in der Highscore." class="award award_failed"/>
                            <img src="images/ape_grey_3.png" alt="Dritter Platz!" title="Werde dritter  in der Highscore." class="award award_failed"/>
                        <!-- /ko -->
                    <!-- /ko -->
                <!-- /ko-->
                <!-- ko if: finished1 > 4 -->
                    <img src="images/ape_diff_1.png" alt="Schwierigkeit 1!" title="Du hast mehr als fünf Boulder mit Schwierigkeit 1 beendet." class="award"/>
                <!-- /ko -->
                <!-- ko ifnot: finished1 > 4 -->
                    <img src="images/ape_diff_grey_1.png" alt="Schwierigkeit 1!" title="Beende mindestens fünf Boulder mit Schwierigkeit 1!" class="award award_failed"/>
                <!-- /ko -->
                
                <!-- ko if: finished2 > 4 -->
                    <img src="images/ape_diff_2.png" alt="Schwierigkeit 2!" title="Du hast mehr als fünf Boulder mit Schwierigkeit 2 beendet." class="award"/>
                <!-- /ko -->
                <!-- ko ifnot: finished2 > 4 -->
                    <img src="images/ape_diff_grey_2.png" alt="Schwierigkeit 2!" title="Beende mindestens fünf Boulder mit Schwierigkeit 2!" class="award award_failed"/>
                <!-- /ko -->
                
                <!-- ko if: finished3 > 4 -->
                    <img src="images/ape_diff_3.png" alt="Schwierigkeit 3!" title="Du hast mehr als fünf Boulder mit Schwierigkeit 3 beendet." class="award"/>
                <!-- /ko -->
                <!-- ko ifnot: finished3 > 4 -->
                    <img src="images/ape_diff_grey_3.png" alt="Schwierigkeit 3!" title="Beende mindestens fünf Boulder mit Schwierigkeit 3!" class="award award_failed"/>
                <!-- /ko -->
                
                <!-- ko if: finished4 > 4 -->
                    <img src="images/ape_diff_4.png" alt="Schwierigkeit 4!" title="Du hast mehr als fünf Boulder mit Schwierigkeit 4 beendet." class="award"/>
                <!-- /ko -->
                <!-- ko ifnot: finished4 > 4 -->
                    <img src="images/ape_diff_grey_4.png" alt="Schwierigkeit 4!" title="Beende mindestens fünf Boulder mit Schwierigkeit 4!" class="award award_failed"/>
                <!-- /ko -->
                
                <!-- ko if: finished5 > 0 -->
                    <img src="images/ape_diff_5.png" alt="Schwierigkeit 5!" title="Du hast einen Boulder mit Schwierigkeit 5 beendet." class="award"/>
                <!-- /ko -->
                <!-- ko ifnot: finished5 > 0 -->
                    <img src="images/ape_diff_grey_5.png" alt="Schwierigkeit 5!" title="Beende mindestens einen Boulder mit Schwierigkeit 5!" class="award award_failed"/>
                <!-- /ko -->
                <h2>Privacy</h2>
                <div>
	            <input type="checkbox" data-role="none" data-inline="true" data-bind="checked: $root.userPrivate" /> Nutzer wird nicht in der Highscore und in der Liste der Finisher angezeigt
	                            
                <div data-role="button" data-mini="true" data-inline="true" data-bind="click: $root.saveUser">speichern</div>
                </div>
                <h2>Beendete Boulder</h2>
                Hier findet man alle Boulder, die man bereits geschafft hat. Klickt auf einen der Boulder, um ihn euch nochmal in Ruhe anzusehen. Ihr könnt den Link auch an einen Freund schicken um ihn vor eine neue Aufgabe zu setzen.
                <ul data-bind="foreach: finishedBoulders">
                    <li>
                        <span class="annotation" data-bind="text:Date"></span>
                        
                        <!-- ko if: Deleted == '1' -->
                        <span data-bind="text:Name"></span> 
                        <!-- /ko -->
                        
                        <!-- ko if: Deleted == '0' -->
                        <a data-bind="click: function(data, event) { viewmodel.changeSelection(WallId,Id)}" href="#"><span data-bind="text:Name"></span></a>
                        <!--<a data-bind="attr: {href: '<?php echo(_FACEBOOK_CHANNEL_URL); ?>?wall_id='+WallId+'&boulder_id='+Id}" target="_blank" data-role="none" class="annotation">(URL)</a>-->
                        <!-- /ko -->
                    </li>
                </ul>
                
            </div>
        </div>
        
        
        <!-- VIEW HIGHSCORE -->
        <div data-bind="visible: highscoreMode" id="canvas_highscore">
            <div>
                <div style="float: left">
                    <div data-role="button" data-inline="true" data-mini="true" data-bind="click: $root.showHighscoreAll">Alle Nutzer</div>
                    <div data-role="button" data-inline="true" data-mini="true" data-bind="click: $root.showHighscoreFriends">Nur Freunde</div>
                </div>
                
                <div style="float: right">
                    <b>Sortierung:</b> 
                    <div data-role="button" data-inline="true" data-mini="true" data-bind="click: $root.sortHighscoreByDiff">BamBunkde</div>
                    <div data-role="button" data-inline="true" data-mini="true" data-bind="click: $root.sortHighscoreByAvgDiff">Schwierigkeit</div>
                </div>
                <ol data-role="listview" data-theme="b" >
                    <!-- ko foreach: highscore -->
                    <li style="clear: both" data-bind="visible: $root.isFriend(facebook_id) || ($root.user()?$root.user().id == user_id : false)"> 
                        <div>
                            <div style="float: left; margin-right: 10px" >
                                <fb:profile-pic data-bind="attr: {uid: facebook_id}" facebook-logo="true" linked="false" size="square"></fb:profile-pic>
                                <img data-bind="visible: rank == 1" src="images/ape_1.png" style="position: absolute;z-index: 1000; left: 25px; top: 60px;" alt="Erster Platz!"/>
                                <img data-bind="visible: rank == 2" src="images/ape_2.png" style="position: absolute;z-index: 1000; left: 25px; top: 60px;" alt="Zweiter Platz!"/>
                                <img data-bind="visible: rank == 3" src="images/ape_3.png" style="position: absolute;z-index: 1000; left: 25px; top: 60px;" alt="Dritter Platz!"/>
                            </div>
                            <div style="margin-left: 70px;" data-bind="css:{ highscore_me: $root.user()?$root.user().id == user_id : false }">
                                <h1 >
                                    <span data-bind="text: user_name"></span>
                                </h1>
                                <p>
                                    <b>Bam-Bunkde*: <span data-bind="text: score"></span></b>
                                    
                                </p>
                                <p>
                                    <b>Durchschnittliche Schwierigkeit**: <span data-bind="text: diff_average"></span></b> 
                                </p>
                                <div style="width:100%; height:10px; margin-bottom: 10px;" class="annotation" >
                                        <div data-bind="style: {width: Math.round(score*2)+'px'}" style="background-color: #FF0000;">&nbsp;</div>
                                
                                </div>
                                <p>
                                    <div data-role="button" data-mini="true" data-inline="true" data-bind="click: loadFinishedBoulders">Tops: <span data-bind="text: finished"></span></div> 
                                    <div data-bind="visible: openFinishedBoulders" class="annotation">
                                    <ul data-bind="foreach: finishedBoulders">
                                        <li>
                                            <span data-bind="text: name, click: function(data, event) { viewmodel.changeSelection(wall_id,id)}" class="link"></span>
                                            (Schwierigkeit: <span data-bind="text: difficulty"></span>, Finished: <span data-bind="text: date"></span>)
                                        </li> 
                                    </ul>
                                </div>
                                </p>
                            </div>
                            
                        </div>
                        
                        
                        
                    </li>
                    <!-- /ko -->
                </ol>
                
                <div class="annotation" style="margin: 10px;">
                    <p>* Summe aller durchschnittlichen Schwierigkeitsbewertung von allen Bouldern, die man geschafft hat. Nicht bewertete Boulder werden als Schwierigkeit 1 gewertet.</p>
                    <p>** Durchschnitt berechnet aus den tatsächlich bewerteten Bouldern.</p>
                </div>
            </div>
        </div>
        
        <!-- BOULDER OVERVIEW -->
        <div data-bind="visible: gridMode" class="grid">
            <a href="#popup_floorplan" data-rel="popup" style="float: right;" data-role="button" data-position-to="window" data-transition="fade"><img src="images/Grundriss_small.png" /></a>
            
            <h2>Übersicht</h2>
            
            <div class="grid_legend">
                <p>
                    <span class="boulder_diff_bg_0 boulder_diff_bg">&nbsp;&nbsp;&nbsp;</span>Keine Bewertung 
                </p>
                <p> 
                    <span class="boulder_diff_bg_1 boulder_diff_bg">&nbsp;&nbsp;&nbsp;</span>Anfänger 
                </p>
                <p> 
                    <span class="boulder_diff_bg_2 boulder_diff_bg">&nbsp;&nbsp;&nbsp;</span>Hobbyboulderer 
                </p>
                <p>
                    <span class="boulder_diff_bg_3 boulder_diff_bg">&nbsp;&nbsp;&nbsp;</span>Fortgeschrittener   
                </p>
                <p>
                    <span class="boulder_diff_bg_4 boulder_diff_bg">&nbsp;&nbsp;&nbsp;</span>Profi    
                </p>
                <p>
                    <span class="boulder_diff_bg_5 boulder_diff_bg">&nbsp;&nbsp;&nbsp;</span>Weltmeister  
                </p>
            </div>
            
            <div id="overview">
                <label for="slider-diff-min">Minimale Schwierigkeit:</label>
                <input type="range" name="slider-diff-min" id="slider-step" value="0" min="0" max="5" step="1" data-bind="value: minDiff" data-highlight="true" data-mini="true"/>
                <label for="slider-diff-max">Maximale Schwierigkeit:</label>
                <input type="range" name="slider-diff-max" id="slider-step" value="5" min="0" max="5" step="1" data-bind="value: maxDiff" data-highlight="true" data-mini="true"/>
               <!-- ko foreach: overview-->
                <div style="clear: both; height: 20px;" data-role="collapsible-set">
                    <div data-role="collapsible" data-collapsed="false">
                        <h3 class="grid_wall" data-bind="text: wall_name"></h3>         
                        <div class="grid_boulders" >
                            <!-- ko foreach: boulders-->
                            <div class="grid_boulder" data-bind="
                                    click: function(data, event) { viewmodel.changeSelection($parent.wall_id,boulder_id)},
                                    css: { 
                                            boulder_diff_0: Math.ceil(max_difficulty) == 0,
                                            boulder_diff_1: Math.ceil(max_difficulty) == 1,
                                            boulder_diff_2: Math.ceil(max_difficulty) == 2,
                                            boulder_diff_3: Math.ceil(max_difficulty) == 3,
                                            boulder_diff_4: Math.ceil(max_difficulty) == 4,
                                            boulder_diff_5: Math.ceil(max_difficulty) == 5,
                                            finished: finished
                                        },
                                        visible: average_difficulty <= $root.maxDiff() && average_difficulty >= $root.minDiff()">
                                <h1>
                                    <span data-bind="text: boulder_name"></span>
                                    <span data-bind="visible: finished">*</span>
                                </h1>
                                <p>
                                    <b>Tops: <span data-bind="text: count_finished"></span></b> 
                                </p>
                                <p>
                                    <div data-bind="visible: max_difficulty">
                                        Schwierigkeit (Max/Avg) <span data-bind="text: max_difficulty"></span> / <span data-bind="text: average_difficulty"></span>
                                    </div>
                                    <div data-bind="visible: max_rating">
                                        Spassfaktor (Max/Avg) <span data-bind="text: max_rating"></span> / <span data-bind="text: average_rating"></span>                        
                                    </div>
                                </p>
                            </div>
                            <!-- /ko -->
                        </div>
                    </div>
                </div>
                <!-- /ko -->  
                <div style="clear: both; height: 40px;"></div>
                 
            </div>
            <div class="annotation">*Boulder bereits beendet</div>
        </div>
        
        <div data-bind="visible: printMode">
            <input type="button" data-bind="click: changeToSelectionMode" value="Zur Normalansicht wechseln" data-mini="true" data-inline="true"/>
        </div>
        
        <div data-bind="visible: newsMode" style="margin: 10px;">
            <h2>Neuigkeiten</h2>
            <div data-bind="foreach: news">
                <a data-bind="click: function(data, event) { viewmodel.changeSelection(wall_id,boulder_id)}" style="cursor: pointer">
                    <!-- ko if: type == 1 -->
                    <div class="global_news news_newboulder">
                        <div data-bind="text: date" class="date"></div>
                        Neuer Boulder "<span data-bind="text: bouldername"></span>" wurde an der Wand "<span data-bind="text: wallname"></span>" definiert.
                    </div>
                    <!-- /ko -->
                    <!-- ko if: type == 2 -->
                    <div class="global_news news_newcompleted">
                        <div data-bind="text: date" class="date"></div>
                        <span data-bind="text: username"></span> hat "<span data-bind="text: bouldername"></span>" beendet.
                    </div>
                    <!-- /ko -->
                </a>
            </div>
        </div>
        
        
        <!-- FOOTER -->
        <div data-role="footer"  data-bind="visible: !printMode()">
            <div class="annotation" style="margin: 10px;">
                <a type="button" data-bind="click: newBoulder" data-mini="true" data-inline="true" >Neuen Boulder erstellen</a>
                <a type="button" data-bind="click: changeToPrintMode" data-mini="true" data-inline="true">Druckansicht</a>
                <a href="http://board.rocknclimb.de/viewforum.php?f=3" target="_blank" data-inline="true" data-mini="true" data-role="button">Forum</a>
                <a href="#popup_impressum" data-rel="popup" data-inline="true" data-mini="true" data-role="button" data-position-to="window" data-transition="fade">Impressum</a>
                
                <div style="float: right;" data-bind="with: stats">
                    Anzahl der Boulder: <span data-bind="text: boulder_count"></span>, Anzahl der Nutzer: <span data-bind="text: user_count"></span>
                </div>
            </div>
	    </div><!-- /footer -->
        
        <!-- /content -->
        
        <!-- POPUP INFO -->
        <div data-role="popup" id="popup_info"  class="ui-content" data-overlay-theme="a" >
	        <a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>
            <h4>Anleitung</h4>
            <ul data-role="none">
                <li>Benennt zunächst den Boulder, die Beschreibung ist optional.</li>
                <li>Danach speichert den Boulder.</li>
                <li>Nun klickt einfach auf die Giffe, die zum Boulder gehören um diese zu markieren.</li>
                <li>Um einzelne Markierungen zu ändern, klickt ihr auf eine der Markierungen verschiebt, beschriftet oder verändert seine Größe. Die Bearbeitung dann mit "Ok" bestättigen.</li>
                <li>Fertig ist der Boulder.</li>
            </ul>
        </div>
        
        <!-- POPUP FLOORPLAN -->
        <div data-role="popup" id="popup_floorplan"  class="ui-content" data-overlay-theme="a" >
	        <a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>
            <img src="images/Grundriss.png" alt="Grundriss"/>
        </div>
        
        <!-- POPUP IMPRESSUM -->
        <div data-role="popup" id="popup_impressum"  class="ui-content" data-overlay-theme="a" >
	        <a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>
            <div>
                <h1>Impressum</h1><p>Angaben gemäß § 5 TMG:<br/><br/></p>
                <p>Christian Matyas<br />
                96052 Bamberg<br />
                </p>
                <h2>Kontakt:</h2>
                <table><tr>
                <tr><td><p>E-Mail:</p></td>
                <td><p>christian.matyas(at)gmail.com</p></td>
                </tr></table>
                <p> </p>
                <p>Quelle: <i>Erstellt durch den <a href="http://www.e-recht24.de">Impressum-Generator</a> von e-recht24.de für Privatpersonen.</i></p>
                <h2>Haftungsausschluss:</h2>
                <p><strong>Haftung für Inhalte</strong></p> <p>Die Inhalte unserer Seiten wurden mit größter Sorgfalt erstellt. Für die Richtigkeit, Vollständigkeit und Aktualität der Inhalte können wir jedoch keine Gewähr übernehmen. Als Diensteanbieter sind wir gemäß § 7 Abs.1 TMG für eigene Inhalte auf diesen Seiten nach den allgemeinen Gesetzen verantwortlich. Nach §§ 8 bis 10 TMG sind wir als Diensteanbieter jedoch nicht verpflichtet, übermittelte oder gespeicherte fremde Informationen zu überwachen oder nach Umständen zu forschen, die auf eine rechtswidrige Tätigkeit hinweisen. Verpflichtungen zur Entfernung oder Sperrung der Nutzung von Informationen nach den allgemeinen Gesetzen bleiben hiervon unberührt. Eine diesbezügliche Haftung ist jedoch erst ab dem Zeitpunkt der Kenntnis einer konkreten Rechtsverletzung möglich. Bei Bekanntwerden von entsprechenden Rechtsverletzungen werden wir diese Inhalte umgehend entfernen.</p> <p><strong>Haftung für Links</strong></p> <p>Unser Angebot enthält Links zu externen Webseiten Dritter, auf deren Inhalte wir keinen Einfluss haben. Deshalb können wir für diese fremden Inhalte auch keine Gewähr übernehmen. Für die Inhalte der verlinkten Seiten ist stets der jeweilige Anbieter oder Betreiber der Seiten verantwortlich. Die verlinkten Seiten wurden zum Zeitpunkt der Verlinkung auf mögliche Rechtsverstöße überprüft. Rechtswidrige Inhalte waren zum Zeitpunkt der Verlinkung nicht erkennbar. Eine permanente inhaltliche Kontrolle der verlinkten Seiten ist jedoch ohne konkrete Anhaltspunkte einer Rechtsverletzung nicht zumutbar. Bei Bekanntwerden von Rechtsverletzungen werden wir derartige Links umgehend entfernen.</p> <p><strong>Urheberrecht</strong></p> <p>Die durch die Seitenbetreiber erstellten Inhalte und Werke auf diesen Seiten unterliegen dem deutschen Urheberrecht. Die Vervielfältigung, Bearbeitung, Verbreitung und jede Art der Verwertung außerhalb der Grenzen des Urheberrechtes bedürfen der schriftlichen Zustimmung des jeweiligen Autors bzw. Erstellers. Downloads und Kopien dieser Seite sind nur für den privaten, nicht kommerziellen Gebrauch gestattet. Soweit die Inhalte auf dieser Seite nicht vom Betreiber erstellt wurden, werden die Urheberrechte Dritter beachtet. Insbesondere werden Inhalte Dritter als solche gekennzeichnet. Sollten Sie trotzdem auf eine Urheberrechtsverletzung aufmerksam werden, bitten wir um einen entsprechenden Hinweis. Bei Bekanntwerden von Rechtsverletzungen werden wir derartige Inhalte umgehend entfernen.</p> <p><strong>Datenschutz</strong></p> <p>Die Nutzung unserer Webseite ist in der Regel ohne Angabe personenbezogener Daten möglich. Soweit auf unseren Seiten personenbezogene Daten (beispielsweise Name, Anschrift oder eMail-Adressen) erhoben werden, erfolgt dies, soweit möglich, stets auf freiwilliger Basis. Diese Daten werden ohne Ihre ausdrückliche Zustimmung nicht an Dritte weitergegeben. </p> <p>Wir weisen darauf hin, dass die Datenübertragung im Internet (z.B. bei der Kommunikation per E-Mail) Sicherheitslücken aufweisen kann. Ein lückenloser Schutz der Daten vor dem Zugriff durch Dritte ist nicht möglich. </p> <p>Der Nutzung von im Rahmen der Impressumspflicht veröffentlichten Kontaktdaten durch Dritte zur Übersendung von nicht ausdrücklich angeforderter Werbung und Informationsmaterialien wird hiermit ausdrücklich widersprochen. Die Betreiber der Seiten behalten sich ausdrücklich rechtliche Schritte im Falle der unverlangten Zusendung von Werbeinformationen, etwa durch Spam-Mails, vor.</p><p> </p>
                <p><strong>Datenschutzerklärung für die Nutzung von Facebook-Plugins (Like-Button)</strong></p> <p>Auf unseren Seiten sind Plugins des sozialen Netzwerks Facebook, 1601 South California Avenue, Palo Alto, CA 94304, USA integriert. Die Facebook-Plugins erkennen Sie an dem Facebook-Logo oder dem "Like-Button" ("Gefällt mir") auf unserer Seite. Eine Übersicht über die Facebook-Plugins finden Sie hier: <a href="http://developers.facebook.com/docs/plugins/" target="_blank">http://developers.facebook.com/docs/plugins/</a>.<br /> Wenn Sie unsere Seiten besuchen, wird über das Plugin eine direkte Verbindung zwischen Ihrem Browser und dem Facebook-Server hergestellt. Facebook erhält dadurch die Information, dass Sie mit Ihrer IP-Adresse unsere Seite besucht haben. Wenn Sie den Facebook "Like-Button" anklicken während Sie in Ihrem Facebook-Account eingeloggt sind, können Sie die Inhalte unserer Seiten auf Ihrem Facebook-Profil verlinken. Dadurch kann Facebook den Besuch unserer Seiten Ihrem Benutzerkonto zuordnen. Wir weisen darauf hin, dass wir als Anbieter der Seiten keine Kenntnis vom Inhalt der übermittelten Daten sowie deren Nutzung durch Facebook erhalten. Weitere Informationen hierzu finden Sie in der Datenschutzerklärung von facebook unter <a href="http://de-de.facebook.com/policy.php" target="_blank"> http://de-de.facebook.com/policy.php</a></p> <p>Wenn Sie nicht wünschen, dass Facebook den Besuch unserer Seiten Ihrem Facebook-Nutzerkonto zuordnen kann, loggen Sie sich bitte aus Ihrem Facebook-Benutzerkonto aus.</p><p> </p>
                <p><strong>Datenschutzerklärung für die Nutzung von Google Analytics</strong></p> <p>Diese Website benutzt Google Analytics, einen Webanalysedienst der Google Inc. ("Google"). Google Analytics verwendet sog. "Cookies", Textdateien, die auf Ihrem Computer gespeichert werden und die eine Analyse der Benutzung der Website durch Sie ermöglichen. Die durch den Cookie erzeugten Informationen über Ihre Benutzung dieser Website werden in der Regel an einen Server von Google in den USA übertragen und dort gespeichert. Im Falle der Aktivierung der IP-Anonymisierung auf dieser Webseite wird Ihre IP-Adresse von Google jedoch innerhalb von Mitgliedstaaten der Europäischen Union oder in anderen Vertragsstaaten des Abkommens über den Europäischen Wirtschaftsraum zuvor gekürzt.</p> <p>Nur in Ausnahmefällen wird die volle IP-Adresse an einen Server von Google in den USA übertragen und dort gekürzt. Im Auftrag des Betreibers dieser Website wird Google diese Informationen benutzen, um Ihre Nutzung der Website auszuwerten, um Reports über die Websiteaktivitäten zusammenzustellen und um weitere mit der Websitenutzung und der Internetnutzung verbundene Dienstleistungen gegenüber dem Websitebetreiber zu erbringen. Die im Rahmen von Google Analytics von Ihrem Browser übermittelte IP-Adresse wird nicht mit anderen Daten von Google zusammengeführt.</p> <p>Sie können die Speicherung der Cookies durch eine entsprechende Einstellung Ihrer Browser-Software verhindern; wir weisen Sie jedoch darauf hin, dass Sie in diesem Fall gegebenenfalls nicht sämtliche Funktionen dieser Website vollumfänglich werden nutzen können. Sie können darüber hinaus die Erfassung der durch das Cookie erzeugten und auf Ihre Nutzung der Website bezogenen Daten (inkl. Ihrer IP-Adresse) an Google sowie die Verarbeitung dieser Daten durch Google verhindern, indem sie das unter dem folgenden Link verfügbare Browser-Plugin herunterladen und installieren: <a href="http://tools.google.com/dlpage/gaoptout?hl=de">http://tools.google.com/dlpage/gaoptout?hl=de</a>.<p> </p>
                <p><strong>Datenschutzerklärung für die Nutzung von Google +1</strong></p> <p><i>Erfassung und Weitergabe von Informationen:</i><br /> Mithilfe der Google +1-Schaltfläche können Sie Informationen weltweit veröffentlichen. über die Google +1-Schaltfläche erhalten Sie und andere Nutzer personalisierte Inhalte von Google und unseren Partnern. Google speichert sowohl die Information, dass Sie für einen Inhalt +1 gegeben haben, als auch Informationen über die Seite, die Sie beim Klicken auf +1 angesehen haben. Ihre +1 können als Hinweise zusammen mit Ihrem Profilnamen und Ihrem Foto in Google-Diensten, wie etwa in Suchergebnissen oder in Ihrem Google-Profil, oder an anderen Stellen auf Websites und Anzeigen im Internet eingeblendet werden.<br /> Google zeichnet Informationen über Ihre +1-Aktivitäten auf, um die Google-Dienste für Sie und andere zu verbessern. Um die Google +1-Schaltfläche verwenden zu können, benötigen Sie ein weltweit sichtbares, öffentliches Google-Profil, das zumindest den für das Profil gewählten Namen enthalten muss. Dieser Name wird in allen Google-Diensten verwendet. In manchen Fällen kann dieser Name auch einen anderen Namen ersetzen, den Sie beim Teilen von Inhalten über Ihr Google-Konto verwendet haben. Die Identität Ihres Google-Profils kann Nutzern angezeigt werden, die Ihre E-Mail-Adresse kennen oder über andere identifizierende Informationen von Ihnen verfügen.<br /> <br /> <i>Verwendung der erfassten Informationen:</i><br /> Neben den oben erläuterten Verwendungszwecken werden die von Ihnen bereitgestellten Informationen gemäß den geltenden Google-Datenschutzbestimmungen genutzt. Google veröffentlicht möglicherweise zusammengefasste Statistiken über die +1-Aktivitäten der Nutzer bzw. gibt diese an Nutzer und Partner weiter, wie etwa Publisher, Inserenten oder verbundene Websites. </p><p> </p>
                <p><strong>Datenschutzerklärung für die Nutzung von Twitter</strong></p> <p>Auf unseren Seiten sind Funktionen des Dienstes Twitter eingebunden. Diese Funktionen werden angeboten durch die Twitter Inc., Twitter, Inc. 1355 Market St, Suite 900, San Francisco, CA 94103, USA. Durch das Benutzen von Twitter und der Funktion "Re-Tweet" werden die von Ihnen besuchten Webseiten mit Ihrem Twitter-Account verknüpft und anderen Nutzern bekannt gegeben. Dabei werden auch Daten an Twitter übertragen.</p> <p>Wir weisen darauf hin, dass wir als Anbieter der Seiten keine Kenntnis vom Inhalt der übermittelten Daten sowie deren Nutzung durch Twitter erhalten. Weitere Informationen hierzu finden Sie in der Datenschutzerklärung von Twitter unter <a href="http://twitter.com/privacy" target="_blank">http://twitter.com/privacy</a>.</p> <p>Ihre Datenschutzeinstellungen bei Twitter können Sie in den Konto-Einstellungen unter <a href="http://twitter.com/account/settings" target="_blank">http://twitter.com/account/settings</a> ändern.</p> <p> </p>
                <p><i>Quellenangaben: <a href="http://www.e-recht24.de/muster-disclaimer.htm" target="_blank">eRecht24 Disclaimer</a>, <a href="http://www.e-recht24.de/artikel/datenschutz/6590-facebook-like-button-datenschutz-disclaimer.html" target="_blank">Facebook Disclaimer</a>, <a href="http://www.google.com/intl/de/analytics/privacyoverview.html" target="_blank">Datenschutzerklärung Google Analytics</a>, <a href="http://www.google.com/intl/de/+/policy/+1button.html" target="_blank">Datenschutzerklärung für Google +1</a>, <a href="http://twitter.com/privacy" target="_blank">Datenschutzerklärung Twitter</a></i></p>
            </div>
        </div>
        
        <!-- POPUP LOGIN -->
        <div data-role="popup" id="popup_login"  class="ui-content" data-overlay-theme="a"  style="width: 500px;">
	        <a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>
            <div class="ui-grid-a" style="padding: 10px;">
	            <div class="ui-block-a">
                    <h3>Login mit Facebook</h3>
                    <a data-bind="click: loginFacebook" type="button" data-mini="true" data-inline="true">
                        <img src="images/facebook_small.png" alt="Mit Hilfe von Facebook anmelden" title="Mit Hilfe von Facebook anmelden"/> Anmelden
                    </a>
                </div>
                <div class="ui-block-b">
                    <h3>Login mit Nutzername</h3>
                    <div>Name: <input type="text" data-bind="value: login_username"></input></div>
                    <div>Passwort: <input type="password" data-bind="value: login_password"></input></div>
                    <div data-bind="text: login_message"></div>
                    <div data-role="button" data-bind="click: loginUser">Anmelden</div>
                    
                    <hr style="margin-top: 35px;"/>
                    <h4>Neuer Nutzer</h4>
                    <div>Name: <input type="text"  data-bind="value: register_username"></input></div>
                    <div>Passwort: <input type="password" data-bind="value: register_password1"></input></div>
                    <div>Passwort (wiederholen): <input type="password" data-bind="value: register_password2"></input></div>
                    <div data-bind="text: register_message"></div>
                    <div data-role="button" data-bind="click: registerUser">Registrieren</div>
                </div>
            </div><!-- /grid-a -->
        </div>
    </div> 
    <!-- /page -->
    
    
    
    <!-- GOOGLE PLUS SCRIPT (Has to be added after the last +1 button).
    <script type="text/javascript">
        window.___gcfg = {lang: 'de'};

        (function() {
        var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
        po.src = 'https://apis.google.com/js/plusone.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
        })();
    </script>
     -->
	</body>
</html>