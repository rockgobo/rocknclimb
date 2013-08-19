var viewmodel;

function BoulderViewModel (app_id, wall_id_t, boulder_id_t)
{
    var self = this;

    self.AppId = app_id;

    self.LoadingBoulders = ko.observable(true);
    self.LoadingHighscore = ko.observable(true);
    self.LoadingFinishers = ko.observable(true);
    self.LoadingOverview = ko.observable(true);
    self.LoadingUser = ko.observable(false);
    self.LoadingNews = ko.observable(true);
    self.Loading = ko.computed(function () {
        return self.LoadingBoulders() || self.LoadingFinishers() || self.LoadingUser();
    });

    self.wall_id_t = wall_id_t;
    self.boulder_id_t = boulder_id_t;

    self.walls = ko.observableArray([]);
    self.boulders = ko.observableArray(null);
    self.nodes = ko.observableArray(null);

    var scale = Math.round((window.innerWidth / 2000) * 10) / 10; 
    self.scale = ko.observable(scale?scale:0.6);
    self.image_width = ko.observable(200);
    self.image_height = ko.observable(200);
    self.offsetX = 0;
    self.offSetY = 0;


    //MODES 
    self.selectionMode = ko.observable(true);
    self.editMode = ko.observable(false);
    self.editModeSingleNode = ko.observable(false);
    self.profileMode = ko.observable(false);
    self.highscoreMode = ko.observable(false);
    self.gridMode = ko.observable(false);
    self.printMode = ko.observable(false);
    self.newsMode = ko.observable(false);

    self.oldScale = ko.observable(false);

    self.changeToSelectionMode = function () {
        if (self.oldScale()) {
            self.scale(self.oldScale());
            self.oldScale(false);
        }
        self.selectionMode(true);
        self.editMode(false);
        self.profileMode(false);
        self.highscoreMode(false);
        self.gridMode(false);
        self.printMode(false);
        self.newsMode(false);
    };
    self.changeToEditMode = function () {
        self.selectionMode(false);
        self.editMode(true);
        self.profileMode(false);
        self.highscoreMode(false);
        self.gridMode(false);
        self.printMode(false);
        self.newsMode(false);
    };

    self.changeToProfileMode = function () {
        self.selectionMode(false);
        self.editMode(false);
        self.profileMode(true);
        self.highscoreMode(false);
        self.gridMode(false);
        self.printMode(false);
        self.newsMode(false);
    };
    self.changeToHighscoreMode = function () {
        if (self.LoadingHighscore()) {
            self.loadHighscore();
        }
        self.selectionMode(false);
        self.editMode(false);
        self.profileMode(false);
        self.highscoreMode(true);
        self.gridMode(false);
        self.printMode(false);
        self.newsMode(false);
    };
    self.changeToGridMode = function () {
        if (self.LoadingOverview()) {
            self.loadOverview();
        }

        self.selectionMode(false);
        self.editMode(false);
        self.profileMode(false);
        self.highscoreMode(false);
        self.gridMode(true);
        self.printMode(false);
        self.newsMode(false);
    };
    self.changeToPrintMode = function () {
        if (self.scale() > 0.4) {
            self.oldScale(self.scale());
            self.scale(0.4);
        }
        self.selectionMode(true);
        self.editMode(false);
        self.profileMode(false);
        self.highscoreMode(false);
        self.gridMode(false);
        self.printMode(true);
        self.newsMode(false);
    };
    self.changeToNewsMode = function () {
        if (self.LoadingNews()) {
            self.loadNews();
        }
        self.selectionMode(false);
        self.editMode(false);
        self.profileMode(false);
        self.highscoreMode(false);
        self.gridMode(false);
        self.printMode(false);
        self.newsMode(true);
    };
    self.editBoulderMode = ko.observable(new Boulder(null,1));

    //SITE STATISTICS
    self.stats = ko.observable();
    self.loadStats = function() {
        $.getJSON("getStats.php", function(data) {
            self.stats(new Stats(data));
        }).success(function() {
        })
            .error(function() { alert("error on loading stats"); })
            .complete(function() {
            });
    };

    self.currentBoulder = ko.observable(null);
    self.currentBoulderName = ko.computed({
        read: function () {
            if (self.currentBoulder() == null) {
                return "";
            }
            else {
                var name = self.currentBoulder().Name();
                if (self.currentBoulder().Completed() == true) {
                    name += '!';
                }
                return name;
            }
        },
        write: function (value) {
            if (self.currentBoulder() != null) {
                self.currentBoulder().Name = value;
            }
        }
    });
    
    self.currentWall = ko.observable(null);
    self.boulderMapping = ko.computed(function () {
        if (self.currentWall() == null) return {};
        return boulderMapping(self.currentWall().Id);
    });
    
    self.nodetypes = ko.observableArray(NodeTypes);
    self.nodeMapping = ko.computed(function () {
        if (self.currentBoulder() == null) return {};
        return mapping(self.currentBoulder().Id(), self.currentWall().Id);
    }
    );



    

    self.currentWallImage = ko.computed(
        function () {
            if (self.currentWall() == null) {
                return "";
            }
            var image_parts = self.currentWall().Image.split('.');
            
            //resize image div
            var image;
            image = new Image();
            image.src = "images/resolutions/" + image_parts[0] + "_" + (self.scale() * 100) + "." + image_parts[1];
            image.onload = function(){
                $(".image-annotate-canvas").height(image.height);
                $(".image-annotate-canvas").width(image.width);
                $(".image-annotate-canvas").css('background-image', "url('images/resolutions/" + image_parts[0] + "_" + (self.scale() * 100) + "." + image_parts[1] + "')");
            };

            return "url('images/resolutions/" + image_parts[0] + "_" + (self.scale() * 100) + "." + image_parts[1] + "')";
        }
    ); 

    self.currentWallImageFacebook = ko.computed(
        function () {
            if (self.currentWall() == null) {
                return "";
            }
            var image_parts = self.currentWall().Image.split('.');
            
            return "http://rocknclimb.de/images/resolutions/" + image_parts[0] + "_10." + image_parts[1];
        }
    ); 
    
    self.currentWallImageSrc = ko.computed(
        function () {
            if (self.currentWall() == null) {
                return "";
            }
            return "images/"+self.currentWall().Image;
        }
    );

        self.changeWallLeft = function () {
            if (self.currentWall) {
                self.changeWall(self.currentWall().Left);
            }
        };
        self.changeWallRight = function () {
            if (self.currentWall) {
                self.changeWall(self.currentWall().Right);
            }
        };


    self.user = ko.observable(false);
    self.userLoggedin = ko.computed(function () { return self.user(); });
    self.friends = ko.observableArray([]);
    self.userPrivate = ko.observable(0);

    self.saveUser = function() {
        $.get("saveUser.php?id=" + self.user().id + "&private=" + self.userPrivate(), function(data) {

        }).success(function() { alert("Das Profil wurde erfolgreich gespeichert."); })
            .error(function() { alert("error on saving user"); })
            .complete(function() {
            });
    };

    /********************************
    *      LOGIN + REGISTER USER    *
    *********************************/
    self.login_username = ko.observable("");
    self.login_password = ko.observable("");
    self.login_message = ko.observable("");
    self.register_username = ko.observable("");
    self.register_password1 = ko.observable("");
    self.register_password2 = ko.observable("");
    self.register_message = ko.observable("");

    self.loginUser = function () {
        self.register_message("");
        self.login_message("");

        if (self.login_username() && self.login_password) {
            $.get("getUser.php?username=" + self.login_username() + "&password=" + self.login_password().hashCode(), function (data) {
                if (data) {
                    self.parseUserData(data);
                }
                else {
                    self.login_message("Nutzername nicht bekannt oder falsches Password.");
                }
            } .bind(self))
            .success(function () { })
            .error(function () { alert("error on loading user"); })
            .complete(function () {
                self.LoadingUser(false);
            });
        }
        else {
            self.login_message("Bitte geben sie einen Nutzernamen oder Password an.");
        }
    };

    self.registerUser = function () {
        self.register_message("");
        self.login_message("");

        if (self.register_password1() == 0 || self.register_password2() == 0) {
            self.register_message("Bitte geben Sie ein Passwort ein.");
            return;
        }

        if (self.register_password1() != self.register_password2()) {
            self.register_message("Passwörter sind nicht identisch. Bitte wiederholen sie die Eingabe.");
            return;
        }
        if (self.register_username() && self.register_password1 && self.register_password2) {
            $.get("registerUser.php?username=" + self.register_username() + "&password=" + self.register_password1().hashCode(), function (data) {
                if (data) {
                    self.parseUserData(data);
                }
                else {
                    self.register_message("Nutzername schon vergeben.");
                }
            } .bind(self))
            .success(function () { })
            .error(function () { alert("error on register user"); })
            .complete(function () {
                self.LoadingUser(false);
            });
        }
        else {
            self.register_message("Bitte geben sie einen Nutzernamen und ein Passwort ein.");
        }
    };

    self.loginFacebook = function () {
        self.register_message("");
        self.login_message("");

        FB.login(function (response) {
            if (response.authResponse) {
                // connected
                FB.api('/me', function (response) {
                    self.loadUser(response.name, response.id, response.username);

                    //load friends
                    FB.api('/me/friends', function (response) {
                        for (var i = 0; i < response.data.length; ++i) {
                            self.friends.push(response.data[i].id);
                        }
                    } .bind(self));
                } .bind(self));
            } else {
                // cancelled
            }
        });
    };


    self.loadUser = function (name, id, username) {
        $.get("getUser.php?facebook=" + name + "&facebook_id=" + id + "&facebook_username=" + username, function (data) {
            self.parseUserData(data);
        } .bind(self))
            .success(function () { })
            .error(function () { alert("error on loading user by facebook"); })
            .complete(function () {
                self.LoadingUser(false);
            });
    };

    self.parseUserData = function (data) {
        var user = JSON.parse(data);
        self.userPrivate(user.private == "1");
        user.private = 0;
        self.user(user);

        self.LoadingHighscore(true);
        self.LoadingOverview(true);

        FB.XFBML.parse();

        $("#popup_login").popup("close");
    };

    self.completeBoulder = function () {
        if (self.currentBoulder().Completed())
            return;

        $.get("completeBoulder.php?boulder_id=" + self.currentBoulder().Id() + "&user_id=" + self.user().id, function (data) {
            self.currentBoulder().Completed(true);
            self.loadFinishers(self.currentBoulder().Id());
        }).success(function () { })
          .error(function () { alert("error on completing boulder"); })
          .complete(function () { });
    };

    self.checkCompletedBoulders = ko.computed(
        function () {
            //Check if boulder are completed
            if (self.user() != false) {
                var finishedBoulders = self.user().finishedBoulders;

                for (var i = 0; i < self.boulders().length; ++i) {
                    for (var j = 0; j < finishedBoulders.length; ++j) {
                        if (self.boulders()[i].Id() == finishedBoulders[j].Id) {
                            self.boulders()[i].Completed(true);
                            break;
                        }
                    }
                }
            }
        }
    );

        self.loadBoulder = ko.computed(
        function () {
            if (self.currentBoulder() != null) {

                if (self.boulder_id_t) {
                    if (self.currentBoulder().Id() != self.boulder_id_t) {
                        return;
                    }
                }

                self.loadNodesFromCurrentWall();
                /*$.getJSON("getNodes.php?boulder_id=" + self.currentBoulder().Id(), function (data) {
                    self.nodes(ko.mapping.fromJS(data, self.nodeMapping()).nodes());
                }).success(function () {
                    FB.XFBML.parse();
                })
                .error(function () { alert("error on loading nodes"); })
                .complete(function () { });*/
            }
        }
    );

    self.loadNodesFromCurrentWall = function () {
        self.nodes(self.currentBoulder().Nodes());
    };

    self.changeBoulder = function(id) {
        for (var i = 0; i < self.boulders().length; ++i) {
            if (self.boulders()[i].Id() == id) {
                self.currentBoulder(self.boulders()[i]);
                self.loadFinishers(id);

                //HACK: refreshing JQuery mobile UI
                $("#selection_boulders").selectmenu("refresh", true);
            }
        }
    };

        self.loadBoulders = ko.computed(
        function () {
            if (self.currentWall() != null) {
                self.boulders([]);

                if (self.wall_id_t) {
                    if (self.currentWall().Id != self.wall_id_t) {
                        return;
                    }
                }
                
                $.getJSON("getBoulders.php?wall_id=" + self.currentWall().Id, function (data) {
                    self.nodes([]);
                    self.boulders(ko.mapping.fromJS(data, self.boulderMapping()).boulders());
                    self.LoadingBoulders(false);

                })
                .success(function () { })
                .error(function () { alert("error on loading boulders"); })
                .complete(function () {
                    if (self.boulder_id_t) {
                        for (var i = 0; i < self.boulders().length; ++i) {
                            var boulder_load = self.boulders()[i];
                            if (boulder_load.Id() == self.boulder_id_t) {
                                self.currentBoulder(boulder_load);
                                self.boulder_id_t = false;
                            }
                        }
                    }
                    else {
                        self.currentBoulder(self.boulders()[0]);
                    }

                    self.loadFinishers(self.currentBoulder().Id());

                    //HACK: refreshing JQuery mobile UI
                    $("#selection_boulders").selectmenu("refresh", true);
                });
                
            }
        }
    );

    self.changeWall = function(id) {
        for (var i = 0; i < self.walls().length; ++i) {
            if (self.walls()[i].Id == id) {
                self.currentWall(self.walls()[i]);
            }
        }
    };

        self.loadWalls = function () {
            $.getJSON("getWalls.php", function (data) {
                self.walls(data);
            })
        .success(function () { })
        .error(function () { alert("error on loading walls"); })
        .complete(function () {
            if (self.wall_id_t) {
                for (var i = 0; i < self.walls().length; ++i) {
                    var wall_load = self.walls()[i];
                    if (wall_load.Id == self.wall_id_t) {
                        self.currentWall(wall_load);
                        self.wall_id_t = false;
                    }
                }
            }
            else {
                self.currentWall(self.walls()[0]);
            }

            //HACK: refreshing JQuery mobile UI
            $("#selection_walls").selectmenu("refresh", true);

        });
        };

    self.editBoulder = function () {
        self.editBoulderMode(true);
    };


    self.editNode = function (node) {
        if (self.editMode() && !self.editModeSingleNode()) {
            self.editModeSingleNode(true);
            node.edit();
        }
    };

    self.newBoulder = function () {

        if (!self.user()) {
            alert("Bitte melden sie sich zunächst an.");
            $("html, body").animate({ scrollTop: 0 }, "slow");
            return;
        }

        $.get("newBoulder.php", function (data) {
            self.changeToEditMode();

            $("html, body").animate({ scrollTop: 0 }, "slow");

            var boulder = new Boulder(false, self.currentWall().Id, self.user());
            boulder.Name("Projekt " + data);

            self.boulders.push(boulder);
            self.currentBoulder(boulder);
            self.nodes([]);

            //HACK: refreshing JQuery mobile UI
            $("#selection_boulders").selectmenu("refresh", true);

            self.editBoulder();
        } .bind(self))
        .success(function () { })
        .error(function () { alert("error on new project name"); })
        .complete(function () { });
    };

    self.deleteBoulder = function (boulder) {
        if (confirm("Wollen Sie den Boulder wirklich löschen?")) {
            self.boulders.remove(boulder);
            boulder.Deleted(true);
            $.get("deleteBoulder.php?id=" + boulder.Id(), function (data) {
                self.changeToSelectionMode();
            });
        }
    };

    self.changeSelection = function(wall_id, boulder_id) {
        self.boulder_id_t = boulder_id;
        self.changeWall(wall_id);

        self.changeToSelectionMode();
    };

    self.newNode = function () {
        self.nodes.push(new Node(null, self.currentBoulder().Id(), self.currentWall().Id));
    };

    self.removeNode = function (node) {
        if (confirm("Wollen Sie die Griffmarkierung wirklich löschen?")) {
            self.nodes.remove(node);
            $.get("deleteNode.php?id=" + node.id(), function (data) {
            });
        }
        else {
            node.editMode(false);
        }
        self.editModeSingleNode(false);
    };

    self.imageClicked = function(data, event) {
        if (self.editMode() && !self.editModeSingleNode()) {
            if (!self.currentBoulder().Id()) {
                alert("Please first save the boulder before defining the nodes");
                return;
            }

            var originalElement = event.srcElement;
            if (!originalElement) {
                originalElement = event.originalTarget;
            }

            if (event.target.className == "image-annotate-canvas") {

                var canvas = $(".image-annotate-canvas").offset();
                var scrollTop = $(window).scrollTop();

                var x = (document.all) ? window.event.x + document.body.scrollLeft - Math.floor(canvas.left) : event.pageX - Math.floor(canvas.left);
                var y = (document.all) ? window.event.y + document.body.scrollTop - Math.floor(canvas.top) : event.pageY - Math.floor(canvas.top);

                //alert(x + " " + y);

                var node = new Node(null, self.currentBoulder().Id(), self.currentWall().Id)
                node.top_((y / viewmodel.scale()) - (node.height_() / 2));
                node.left_((x / viewmodel.scale()) - (node.width_() / 2));
                node.doSave();
                self.nodes.push(node);
            }
        }
    };

    self.nextBoulder = function () {
        var index = self.boulders.indexOf(self.currentBoulder())
        if (index != -1 && (index + 1) < self.boulders().length) {
            self.currentBoulder(self.boulders()[index + 1]);

            //HACK: refreshing JQuery mobile UI
            $("#selection_boulders").selectmenu("refresh", true);
        }
    };

    self.previousBoulder = function () {
        var index = self.boulders.indexOf(self.currentBoulder())
        if (index != -1 && (index - 1) >= 0) {
            self.currentBoulder(self.boulders()[index - 1]);

            //HACK: refreshing JQuery mobile UI
            $("#selection_boulders").selectmenu("refresh", true);
        }
    };

    self.highlightBoulder = function(node) {
        $(".node_boulder_" + node.boulder()).addClass("node_hightlighted");
    };
    self.unhighlightBoulder = function(node) {
        $(".node_boulder_" + node.boulder()).removeClass("node_hightlighted");
    };

    



    self.highscoreAll = ko.observable(true);
    self.isFriend = function (data) {
        if (self.highscoreAll()) {
            return true;
        }
        return self.friends.indexOf(data + '') != -1;
    };

    self.showHighscoreAll = function() {
        self.highscoreAll(true);
    };
    self.showHighscoreFriends = function() {
        self.highscoreAll(false);
    };

    self.CanEditBoulder = ko.computed(
        function () {
            return self.currentBoulder() && self.user() !== false && self.currentBoulder().UserId() == self.user().id;
        }
    );

        

    self.publishToFacebook = function () {
        FB.ui(
       {
           method: 'feed',
           message: 'finished ' + self.currentBoulder().Name(),
           name: 'User has finished the boulder "' + self.currentBoulder().Name() + '" in Gaustadt.',
           caption: 'Check it out yourself now.',
           description: '',
           picture: self.currentWallImageFacebook(),
           link: 'http://rocknclimb.de/?wall_id=' + self.currentWall().Id + '&boulder_id=' + self.currentBoulder().Id(),
           actions: [
               { name: 'show', link: 'http://rocknclimb.de/?wall_id=' + self.currentWall().Id + '&boulder_id=' + self.currentBoulder().Id() }
           ],
           user_prompt_message: 'Personal message here'
       },
       function (response) {
           if (response && response.post_id) {
               alert('Post was published.');
           } else {
               alert('Post was not published.');
           }
       }
     );
   };

   self.sendChallenge = function () {
       FB.ui({
           method: 'apprequests',
           message: 'Boulder challenge.'
       },
       function (response) {
           alert(response);
       });
   };

   /****************
   *   Comments
   *****************/
   self.newcomment = ko.observable("");
   self.newcomment_rating = ko.observable(0);
   self.newcomment_difficulty = ko.observable(0);

    self.deleteComment = function(comment) {
        $.get(
            "deleteComment.php?id=" + comment.Id(),
            function(data) {
                var tempComments = new Array();
                for (var i = 0; i < self.currentBoulder().Comments().length; ++i) {
                    var _comment = self.currentBoulder().Comments()[i];
                    if (_comment.Id() != comment.Id()) {
                        tempComments.push(_comment);
                    }
                }
                self.currentBoulder().Comments(tempComments);
            }
                .bind(self))
            .success(function() {
            })
            .error(function() { alert("error on deleting comment"); })
            .complete(function() {
            });
    };

   self.addComment = function () {
       if (!self.currentBoulder().Completed()) {
           self.newcomment_rating = ko.observable(0);
           self.newcomment_difficulty = ko.observable(0);
       }

       if (self.newcomment() == "" && self.newcomment_rating() == 0 && self.newcomment_difficulty() == 0) {
           return;
       }

       if (
        self.user() &&
        (self.newcomment_rating() > 0 ||
        self.newcomment_difficulty() > 0)) {
           var updated = false;
           var temp_comments = new Array();
           for (var i = 0; i < self.currentBoulder().Comments().length; ++i) {
               var _comment = self.currentBoulder().Comments()[i];
               if (!updated && _comment.UserId() == self.user().id && (_comment.Rating() > 0 || _comment.Difficulty() > 0)) {
                   temp_comments.push(new Comment({ "Comment": self.newcomment(), "Rating": self.newcomment_rating(), "Difficulty": self.newcomment_difficulty(), "UserId": self.user().id, "UserName": self.user().name, "FacebookId": self.user().facebook_id }));
                   updated = true;

                   alert("Deine Bewertung ist aktualisiert worden.");
               }
               else {
                   temp_comments.push(self.currentBoulder().Comments()[i]);
               }
           }
           if (!updated) {
               temp_comments.push(new Comment({ "Comment": self.newcomment(), "Rating": self.newcomment_rating(), "Difficulty": self.newcomment_difficulty(), "UserId": self.user().id, "UserName": self.user().name, "FacebookId": self.user().facebook_id }));
           }
           self.currentBoulder().Comments.removeAll();
           self.currentBoulder().Comments(temp_comments);
       }
       else {
           self.currentBoulder().Comments.push(new Comment({ "Comment": self.newcomment(), "Rating": self.newcomment_rating(), "Difficulty": self.newcomment_difficulty(), "UserId": self.user().id, "UserName": self.user().name, "FacebookId": self.user().facebook_id }));
       }

       $.get(
            "addComment.php?boulderid=" + self.currentBoulder().Id() + "&comment=" + self.newcomment() + "&rating=" + self.newcomment_rating() + "&difficulty=" + self.newcomment_difficulty() + "&user_id=" + self.user().id,
            function (data) {
                self.newcomment("");
                self.newcomment_rating(0);
                self.newcomment_difficulty(0);
            } .bind(self))
            .success(function () {
                FB.XFBML.parse();
            })
            .error(function () { alert("error on adding comment"); })
            .complete(function () { });
   };

        /****************
        *   Overview
        *****************/
        self.minDiff = ko.observable(0);
        self.maxDiff = ko.observable(5);
    self.overview = ko.observableArray([]);
    self.loadOverview = function () {
        $.get(
            "getBoulderOverview.php" + (self.user() ? "?user_id=" + self.user().id : ""),
            function (data) {
                self.overview(JSON.parse(data));
            }
            .bind(self))
            .success(function () { })
            .error(function () { alert("error on loading overview"); })
            .complete(function () {
                self.LoadingOverview(false);
            });
        };
        
        /****************
        *   Highscore
        *****************/
        self.highscoreMapping = {
            'highscores': {
                create: function (options) {
                    return new Highscore(options.data);
                }
            }
        };

        self.highscore = ko.observableArray([]);
        self.loadHighscore = function () {
            $.get(
            "getHighscore.php" + (self.user() ? "?user_id=" + self.user().id : ""),
            function (data) {

                self.highscore(ko.mapping.fromJS(JSON.parse(data), self.highscoreMapping).highscores());
                self.highscore.sort(function (left, right) {
                    return left.score == right.score ? 0 : (left.score < right.score ? 1 : -1)
                });
            }
            .bind(self))
            .success(function () { })
            .error(function () { alert("error on loading highscore"); })
            .complete(function () {
                self.LoadingHighscore(false);
                FB.XFBML.parse();
            });
        };

        self.sortHighscoreByDiff = function(){
            self.highscore.sort(function (left, right) {
                    return left.score == right.score ? 0 : (left.score < right.score ? 1 : -1)
                });
        };

        self.sortHighscoreByAvgDiff = function () {
            self.highscore.sort(function (left, right) {
                return left.diff_average == right.diff_average ? 0 : (left.diff_average < right.diff_average ? 1 : -1)
                });
        };

        /****************
        *   Finishers
        *****************/
        self.finishers = ko.observableArray([]);
        self.loadFinishers = function (id) {
            $.get(
            "getFinisher.php?boulder_id="+id,
            function (data) {
                self.finishers(JSON.parse(data));
                FB.XFBML.parse();
            }
            .bind(self))
            .success(function () { })
            .error(function () { alert("error on loading finishers"); })
            .complete(function () {
                self.LoadingFinishers(false);
            });
        };

        /****************
        *      NEWS
        *****************/
        self.news = ko.observableArray([]);
        self.loadNews = function () {
            $.get(
            "getNews.php",
            function (data) {
                self.news(JSON.parse(data));
            }
            .bind(self))
            .success(function () { })
            .error(function () { alert("error on loading finishers"); })
            .complete(function () {
                self.LoadingFinishers(false);
            });
        };

        /****************
        * FACEBOOK PAGE
        *****************/
        self.postNewBoulderOnFPage = function (_boulder) {
            var _link = _boulder.getLink();
            var _description = _boulder.Description();
            var _message = "Neuer Boulder wurde eingetragen: " + _boulder.Name() + ": " + _link + ". "+_description;    

            FB.api('/me/accounts', function (response) {
                for (var i = 0; i < response.data.length; ++i) {
                    if (response.data[i].id == '295265667266963') {
                        var _access_token = response.data[i].access_token;
                        $.ajax({
                            type: "post",
                            url: "https://graph.facebook.com/295265667266963/feed",
                            data: {
                                message: _message,
                                //actions: [{ name: "anzeigen", link: _link}],
                                access_token: _access_token
                            },
                            dataType: "json"
                        });
                    }
                }
            });
        };

   self.loadWalls();
   self.loadStats();
   //self.loadOverview();
   //self.loadHighscore();
   //self.loadNews();
};

/*********************************************
*               MAPPINGS
**********************************************/
var mapping = function (boulder_id, wall_id) {
    return {
        'nodes': {
            create: function (options) {
                return new Node(options.data, boulder_id, wall_id);
            },
            key: function (data) {
                return ko.utils.unwrapObservable(data.id);
            }
        }
    };
};

var boulderMapping = function (wall_id) {
    return {
        'boulders': {
            create: function (options) {
                return new Boulder(options.data, wall_id);
            },
            key: function(data) {
                return ko.utils.unwrapObservable(data.Id);
            }
        }
    };
};



/*********************************************
*                HIGHSCORE
**********************************************/
function Highscore(data) {
    var self = this;

    self.user_id = data.user_id;
    self.user_name = data.user_name;
    self.facebook_id = data.facebook_id;
    self.finished = data.finished;
    self.score = data.score;
    self.diff_average = data.diff_average;
    self.rank = data.rank;

    self.openFinishedBoulders = ko.observable(false);
    self.finishedBoulders = ko.observableArray([]);
    self.loadFinishedBoulders = function() {
        if (!self.openFinishedBoulders()) {
            $.get(
                "getFinishedBoulders.php?user_id=" + self.user_id,
                function(data) {
                    self.finishedBoulders(JSON.parse(data));
                    self.openFinishedBoulders(true);
                }
                    .bind(self))
                .success(function() {
                })
                .error(function() { alert("error on loading finished boulder of user " + self.user_name); })
                .complete(function() {
                });
        } else {
            self.openFinishedBoulders(false);
        }
    };
}



/*********************************************
*                 NODE
**********************************************/
function Node(data, boulder_id, wall_id) {
    var self = this;

    self.editMode = ko.observable(false);

    self.id = ko.observable(0);
    self.top_ = ko.observable(10);
    self.left_ = ko.observable(10);
    self.width_ = ko.observable(60);
    self.height_ = ko.observable(60);
    self.text = ko.observable("");
    self.type = ko.observable(0);
    self.boulder = ko.observable(boulder_id);
    self.wall = ko.observable(wall_id);

    if (data) {
        self.id = ko.observable(data.id);
        self.top_ = ko.observable(data.top);
        self.left_ = ko.observable(data.left);
        self.width_ = ko.observable(data.width);
        self.height_ = ko.observable(data.height);
        self.text = ko.observable(data.text);
        self.type = ko.observable(data.type);
    }

    self.top_temp = ko.observable(self.top_());
    self.left_temp = ko.observable(self.left_());
    self.width_temp = ko.observable(self.width_());
    self.height_temp = ko.observable(self.height_());

    self.top_start = ko.observable(self.top_());
    self.left_start = ko.observable(self.left_());
    self.width_start = ko.observable(self.width_());
    self.height_start = ko.observable(self.height_());

    self.top = ko.computed( function () { return (viewmodel.scale()* self.top_()) + "px"; }, self);
    self.left = ko.computed( function () { return (viewmodel.scale() * self.left_()) + "px"; }, self);
    self.width = ko.computed( function () { return (viewmodel.scale() * self.width_()) + "px"; }, self);
    self.height = ko.computed( function () { return (viewmodel.scale() * self.height_()) + "px"; }, self);

    self.textTop = ko.computed( function () { return ((viewmodel.scale() * self.top_temp()) + (viewmodel.scale() * self.height_temp()) + 7) + "px"; }, self);
    self.textLeft = ko.computed( function () { return (viewmodel.scale() * self.left_temp()) + "px"; }, self);

    self.edit = function(data, event) {
        if (self.editMode()) return;

        self.editMode(true);

        self.top_start(self.top_());
        self.left_start(self.left_());
        self.width_start(self.width_());
        self.height_start(self.height_());

        self.top_temp(self.top_());
        self.left_temp(self.left_());
        self.width_temp(self.width_());
        self.height_temp(self.height_());

        $('#node_' + self.id()).resizable({
            handles: "all",
            resize: function(e, ui) {
                self.width_temp(ui.size.width / viewmodel.scale());
                self.height_temp(ui.size.height / viewmodel.scale());
                self.top_temp(ui.position.top / viewmodel.scale());
                self.left_temp(ui.position.left / viewmodel.scale());
            }
        }).draggable({
            containment: ".image-annotate-canvas",
            stop: function(e, ui) {

            },
            drag: function(e, ui) {
                self.top_temp(ui.position.top / viewmodel.scale());
                self.left_temp(ui.position.left / viewmodel.scale());
            }
        });
    };
    self.cancel = function() {
        self.editMode(false);

        self.top_temp(self.top_start());
        self.left_temp(self.left_start());
        self.width_temp(self.width_start());
        self.height_temp(self.height_start());

        alert("temp: " + self.top_temp() + " " + self.left_temp() + " " + self.width_temp() + " " + self.height_temp());

        self.endEdit();
    };
    self.save = function(dataEvent, event) {
        self.editMode(false);
        self.endEdit();
        self.text(dataEvent.text());

        self.doSave();
    };

    self.doSave = function() {
        $.get(
            "save.php?id=" + self.id() + "&top=" + Math.round(self.top_()) + "&left=" + Math.round(self.left_()) + "&width=" + Math.round(self.width_()) + "&height=" + Math.round(self.height_()) + "&text=" + self.text() + "&type=" + self.type() + "&boulder=" + self.boulder() + "&wall_id=" + self.wall(),
            function(data) {
                self.id(data);
            }.bind(self))
            .success(function() {
            })
            .error(function() { alert("error on saving node"); })
            .complete(function() {
            });
    };

    self.endEdit = function () {
        if (self.editMode() == false) {
            //alert("temp: " + self.top_temp() + " " + self.left_temp() + " " + self.width_temp() + " " + self.height_temp());

            self.top_(self.top_temp());
            self.left_(self.left_temp());
            self.width_(self.width_temp());
            self.height_(self.height_temp());

            $('#node_' + self.id()).draggable('destroy');
            $('#node_' + self.id()).resizable('destroy');

            viewmodel.editModeSingleNode(false);
        }
    };

    self.colors = new Array("#FF0000", "#FFFF00", "#40FF00", "#00FFFF", "#0080FF", "#0000FF", "#8000FF", "#FF00FF", "#6E6E6E", "#61210B", "#F5DA81");
    self.randomColor = ko.observable(self.colors[boulder_id % self.colors.length]);
};

/*********************************************
*                 NODE TYPE
**********************************************/
var NodeType = function (id, name) {
    this.id = id;
    this.name = name;
};

var NodeTypes = new Array(
    new NodeType(0, "Griff"),
    new NodeType(1, "Henkel"),
    new NodeType(2, "Slopper"),
    new NodeType(3, "Leiste"),
    new NodeType(4, "Zange"),
    new NodeType(5, "Fingerloch",
    new NodeType(6, "Schuppe"))
);

function getNodeType(id) {
    for (var type in NodeTypes) {
        if (id == type.id) {
            return type;
        }
    }
}

/*********************************************
*                 BOULDER
**********************************************/
function Boulder(data, wall, user) {
    var self = this;
    self.Id = ko.observable(0);
    self.Name = ko.observable("new boulder");
    self.Description = ko.observable("");
    self.WallId = ko.observable(wall);
    self.UserId = ko.observable(0);
    self.UserName = ko.observable("");
    self.Completed = ko.observable(false);
    self.StepGrip = ko.observable(false);
    self.StepSpax = ko.observable(true);
    self.Deleted = ko.observable(false);
    self.Comments = ko.observableArray([]);
    self.Nodes = ko.observableArray([]);
    self.Deleted = ko.observable(false);
    self.Created = ko.observable(new Date());
    self.IsRemix = ko.observable(false);

    if (data) {
        self.Id = ko.observable(data.Id);
        self.Name = ko.observable(data.Name);
        self.Description = ko.observable(data.Description);
        self.UserId = ko.observable(data.UserId);
        self.UserName = ko.observable(data.UserName);
        self.Completed = ko.observable(data.Completed);
        self.StepGrip = ko.observable(data.StepGrip == "1");
        self.StepSpax = ko.observable(data.StepSpax == "1");
        self.Deleted = ko.observable(data.Deleted == "1");
        self.IsRemix = ko.observable(data.IsRemix == "1");

        //Convert MySQL datetime in javascript Date
        var t = data.Created.split(/[- :]/);
        var d = new Date(t[0], t[1] - 1, t[2], t[3], t[4], t[5]);
        self.Created = ko.observable(d);

        for (var i = 0; i < data.Comments.length; ++i) {
            self.Comments.push(new Comment(data.Comments[i]));
        }

        for (var i = 0; i < data.Nodes.length; ++i) {
            self.Nodes.push(new Node(data.Nodes[i],data.Id, self.WallId()));
        }
    }
    if ((typeof user) !== 'undefined' && user !== null && user) {
        self.UserId = ko.observable(user.id);
        self.UserName = ko.observable(user.username);
    }

    self.CreationDate = ko.computed(
        function () {
            return self.Created().getDate() + "." + (self.Created().getMonth()+1) + "." + self.Created().getFullYear();
        }
    );

   self.IsNew = ko.computed(
        function () {
            var today = new Date();   //Startzeitpunkt
            var msec_today = today.getTime();

            var msec_created = self.Created().getTime();

            if (((msec_today - msec_created) / 1000) < 259200) {
                return true;
            }
            else {
                return false;
            }
        }
    );
        self.BoulderName = ko.computed(
        function () {
            if (self.IsNew()) {
                return self.Name() + " ( NEU! )";
            }
            else {
                return self.Name();
            }
        }
    );
        self.Rating = ko.computed(
        function () {
            var rating = 0;
            var count = 0;
            for (var i = 0; i < self.Comments().length; ++i) {
                var _rating = parseInt(self.Comments()[i].Rating());
                if (_rating > 0) {
                    rating += _rating;
                    count += 1;
                }
            }

            if (count > 0) {
                return Math.round((rating / count) * 10) / 10;
            }
            return "-";
        }
    );

        self.Difficulty = ko.computed(
        function () {
            var difficulty = 0;
            var count = 0;
            for (var i = 0; i < self.Comments().length; ++i) {
                var _difficulty = parseFloat(self.Comments()[i].Difficulty());
                if (_difficulty > 0) {
                    difficulty += _difficulty;
                    count += 1;
                }
            }

            if (count > 0) {
                return Math.round((difficulty / count) * 10) / 10;
            }
            return "-";
        }
    );
    
    self.getLink = function(){
        return 'http://rocknclimb.de/index.php?wall_id=' + self.WallId() + '&boulder_id=' + self.Id();
    }; 

    self.save = function () {
        $.get("saveBoulder.php?id=" + self.Id() + "&name=" + self.Name() + "&description=" + self.Description() + "&wall=" + self.WallId() + "&user_id=" + self.UserId() + "&step_grip=" + self.StepGrip() + "&step_spax=" + self.StepSpax() + "&isRemix=" + self.IsRemix(), function (data) {
            if (self.Id() == 0) {
                self.Id(data);
                viewmodel.postNewBoulderOnFPage(self);
            }
            
            viewmodel.editBoulderMode(false);
        }).success(function () { alert("Boulder gespeichert") })
            .error(function () { alert("Fehler beim speichern des Boulders"); })
            .complete(function () {
                //HACK: refreshing JQuery mobile UI
                $("#selection_boulders").selectmenu("refresh", true);
             }); ;
    };
};

function Wall() {
    var self = this;
    self.Id = ko.observable(1);
    self.Name = ko.observable("");
};

function Comment(data) {
    var self = this;
    self.Comment = ko.observable(data.Comment);
    self.Rating = ko.observable(data.Rating);
    self.Difficulty = ko.observable(data.Difficulty);
    self.UserId = ko.observable(data.UserId);
    self.UserName = ko.observable(data.UserName);
    self.FacebookId = ko.observable(data.FacebookId);
    self.Id = ko.observable(data.Id);
}

function Stats(data) 
{
    var self = this;
    self.boulder_count = ko.observable(data.boulder_count);
    self.user_count = ko.observable(data.user_count);
}

String.prototype.hashCode = function () {
    var hash = 0, i, char;
    if (this.length == 0) return hash;
    for (i = 0; i < this.length; i++) {
        char = this.charCodeAt(i);
        hash = ((hash << 5) - hash) + char;
        hash = hash & hash; // Convert to 32bit integer
    }
    return hash;
};
