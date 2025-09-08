<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Yearner</title>
        <link rel="stylesheet" href="style.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    </head>
    
    <body>
        <header class="banner">
            <div class="logo">
                <h3>Yearn<span>ers</span> </h3>
                <hr>
            </div>
            <div class="headtitle">
                <h1>Meet the Group</h1>
            </div>
        </header>

        <div class="main">
            <div class="container">
                <div class="tab_box">
                    <button class="tab_btn active">Home</button>
                    <button class="tab_btn">Members</button>
                </div>
            </div>

            <div class="members">

                <div class="infogrid">
                    <div class="info active" id="home">
                        <section class="about">
                            <div class="about-container">
                                <h3 class="about-title">About Us</h3>
                                    <div class="about-content">
                                        <div class="about-left">
                                            <h1>Hi, <span>we’re the</span><br><strong>Yearners</strong></h1>
                                        </div>
                                        <div class="about-right">
                                            <p>
                                                The Yearner group is a community of individuals who are driven by 
                                                curiosity, passion, and the desire to achieve their dreams. Members 
                                                support and inspire one another to pursue personal growth, share 
                                                knowledge, and work toward common goals. Through collaboration, 
                                                creativity, and encouragement, the group creates a positive environment 
                                                where ideas can flourish and ambitions can turn into reality. Whether in 
                                                learning new skills, tackling challenges, or celebrating achievements, 
                                                the Yearner group embodies the spirit of perseverance and hope.
                                            </p>
                                        </div>
                                    </div>
                            </div>
                        </section>
                    </div>

                    <div class="info">
                        <div class="information" onclick="openModal('johnModal')">
                            <div class="pfp">
                                <img src="pfp.jpg" alt="pfp">
                            </div>
                            <div class="name">
                                <h3>John Vee Perez</h3>
                            </div>
                            <div class="bio">
                                <p>🔹Passionate about Web Development</p>
                                <p>🔹Batak mag Yearn</p>
                            </div>
                            <div class="grid1">
                                <div class="img">
                                    <img src="icons8-html-5-48.png" alt="">
                                </div>
                                <div class="img">
                                    <img src="icons8-css3-48.png" alt="">
                                </div>
                                <div class="img">
                                    <img src="icons8-canva-50.png" alt="">
                                </div>
                            </div>
                                <hr>
                            <div class="grid2">
                                <div class="img">
                                    <img src="icons8-fb-50.png" alt="">
                                </div>
                                <div class="img">
                                    <img src="icons8-instagram-50.png" alt="">
                                </div>
                                <div class="img">
                                    <img src="icons8-youtube-50.png" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="information" onclick="openModal('zanderModal')">
                            <div class="pfp">
                                <img src="zanderpicsquare.jpg" alt="pfp">
                            </div>
                            <div class="name">
                                <h3>Zander Malonzo</h3>
                            </div>
                            <div class="bio">
                                <p>🔹 Passionate about software development and Pang Character Development</p>
                                <p> 🔹 Learning C++, Java, and C# — exploring web</p>
                            </div>
                            <div class="grid1">
                                <div class="img">
                                    <img src="icons8-html-5-48.png" alt="">
                                </div>
                                <div class="img">
                                    <img src="icons8-css3-48.png" alt="">
                                </div>
                                <div class="img">
                                    <img src="icons8-canva-50.png" alt="">
                                </div>
                            </div>
                                <hr>
                            <div class="grid2">
                                <div class="img">
                                    <img src="icons8-fb-50.png" alt="">
                                </div>
                                <div class="img">
                                    <img src="icons8-instagram-50.png" alt="">
                                </div>
                                <div class="img">
                                    <img src="icons8-youtube-50.png" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="information" onclick="openModal('stanleyModal')">
                            <div class="pfp">
                                <img src="stanley.jpg" alt="pfp">
                            </div>
                            <div class="name">
                                <h3>Stanley Visaya</h3>
                            </div>
                            <div class="bio">
                                <p>🔹Passionate about programming and gaming </p>
                                <p>🔹Learning C# and C++</p>
                            </div>
                            <div class="grid1">
                                <div class="img">
                                    <img src="icons8-html-5-48.png" alt="">
                                </div>
                                <div class="img">
                                    <img src="icons8-css3-48.png" alt="">
                                </div>
                                <div class="img">
                                    <img src="icons8-canva-50.png" alt="">
                                </div>
                            </div>
                                <hr>
                            <div class="grid2">
                                <div class="img">
                                    <img src="icons8-fb-50.png" alt="">
                                </div>
                                <div class="img">
                                    <img src="icons8-instagram-50.png" alt="">
                                </div>
                                <div class="img">
                                    <img src="icons8-youtube-50.png" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="johnModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal('johnModal')">&times;</span>
                        
                        <div class="cardinfo">
                            <div class="cardgrid">
                                <div class="cardname">
                                    <h1>John Vee Perez</h1>
                                    <h2>Junior</h2>
                                </div>
                                <div class="Details">
                                    <ul class="PersoProp">
                                        <li><strong>Age:</strong></li>
                                        <li><strong>Birthday:</strong></li>
                                        <li><strong>Address:</strong></li>
                                        <li><strong>Email:</strong></li>
                                        <li><strong>Contact:</strong></li>
                                        <li><strong>Course:</strong></li>
                                        <li><strong>Hobbies:</strong></li>
                                    </ul>
                                    <ul class="PersoVal">
                                        <li>20</li>
                                        <li>September 21, 2004</li>
                                        <li>Las Piñas City</li>
                                        <li>johnveeperezzz@gmail.com</li>
                                        <li>+63 992-212-8929</li>
                                        <li>BSIT</li>
                                        <li>Designing, Programming</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="cardpic">
                                <img src="pfp.jpg" alt="">
                            </div>
                        </div>
                        
                        
                        <div class="carddescription">
                            <p>
                                Hi, I'm Vee!
                                I’m passionate about creating functional and creative projects. 
                                I specialize in building projects that blend creativity and functionality.
                            </p>
                        </div>
                        
                    </div>
                </div>

                <div id="zanderModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal('zanderModal')">&times;</span>
                       
                        <div class="cardinfo">
                            <div class="cardgrid">
                                <div class="cardname">
                                    <h1>Zander Malonzo</h1>
                                    <h2>Junior</h2>
                                </div>
                                <div class="Details">
                                    <ul class="PersoProp">
                                        <li><strong>Age:</strong></li>
                                        <li><strong>Birthday:</strong></li>
                                        <li><strong>Address:</strong></li>
                                        <li><strong>Email:</strong></li>
                                        <li><strong>Contact:</strong></li>
                                        <li><strong>Course:</strong></li>
                                        <li><strong>Hobbies:</strong></li>
                                    </ul>
                                    <ul class="PersoVal">
                                        <li>20</li>
                                        <li>March 8, 2005</li>
                                        <li>Muntinlupa City</li>
                                        <li>zandermalonzo@gmail.com</li>
                                        <li>+63 919-257-5989</li>
                                        <li>BSIT</li>
                                        <li>coffee enthusiast , programming, gaming</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="cardpic">
                                <img src="zanderpicsquare.jpg" alt="">
                            </div>
                        </div>
                        
                        
                        <div class="carddescription">
                            <p>
                                Hi, I'm Zander!
								I’m passionate about creating discord bots and creative projects. 
								I specialize in building projects that blend creativity and functionality.
                            </p>
                        </div>

                    </div>
                </div>

                <div id="stanleyModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal('stanleyModal')">&times;</span>
                       
                        <div class="cardinfo">
                            <div class="cardgrid">
                                <div class="cardname">
                                    <h1>Stanley Visaya</h1>
                                    <h2>Junior</h2>
                                </div>
                                <div class="Details">
                                    <ul class="PersoProp">
                                        <li><strong>Age:</strong></li>
                                        <li><strong>Birthday:</strong></li>
                                        <li><strong>Address:</strong></li>
                                        <li><strong>Email:</strong></li>
                                        <li><strong>Contact:</strong></li>
                                        <li><strong>Course:</strong></li>
                                        <li><strong>Hobbies:</strong></li>
                                    </ul>
                                    <ul class="PersoVal">
                                        <li>20</li>
                                        <li>July 26, 2005</li>
                                        <li>Taguig City</li>
                                        <li>stanleyvisaya@gmail.com</li>
                                        <li>+63 945-502-8055</li>
                                        <li>BSIT</li>
                                        <li>Gaming, Drawing, Programming</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="cardpic">
                                <img src="stanley.jpg" alt="">
                            </div>
                        </div>
                        
                        
                        <div class="carddescription">
                            <p>
                               Stanley Visaya, preferably called Ley, is a 19-year-old Junior BSIT student from Taguig City, 
							is passionate about technology and creativity.
							Her passion in gaming helped a lot in her growth. 
							 Stanley’s love for technology pushes her to explore more about the world of programming.
                            </p>
                        </div>

                    </div>
                </div>


            </div>

        </div>
        
        <footer>
            <div class="credits">
                <p>&copy; <?php echo date("Y"); ?> Yearners Group</p>
            </div>
            <div class="time">
                <p id="clock"></p>
            </div>
                <div style="text-align:center; margin-top:20px;">
                    <form method="get" action="login.php" style="display:inline;">
                        <button type="submit" style="padding:10px 24px; background:#222; color:#fff; border:none; border-radius:8px; font-size:16px; cursor:pointer;">Logout</button>
                    </form>
                </div>
        </footer>

        <?php
            date_default_timezone_set("Asia/Manila");
            $phTime = date("Y-m-d H:i:s");
        ?>

        <script>
            let currentTime = new Date("<?php echo $phTime; ?>");

            function updateClock() {
                currentTime.setSeconds(currentTime.getSeconds() + 1);

                let hours = currentTime.getHours() % 12 || 12;
                let minutes = currentTime.getMinutes().toString().padStart(2, '0');
                let seconds = currentTime.getSeconds().toString().padStart(2, '0');
                let ampm = currentTime.getHours() >= 12 ? 'PM' : 'AM';

                document.getElementById("clock").innerText =
                    "Current Time: " + hours + ":" + minutes + ":" + seconds + " " + ampm;
            }

            updateClock();
            setInterval(updateClock, 1000);
        </script>


        <script src="tabs.js"></script>
        
    </body>
</html>