SimplePoll
===========

What is it?
-----------
SimplePoll is exactly what it sounds like, it's a simple poll.

What does that mean?
--------------------
Well, basically a simple poll in this case is something that can store multiple polls with varying poll
information and option choices without the need of a SQL database.

How does it work?
-----------------
SimplePoll uses a simple PHP class that has various functions to create edit and remove polls 
as well as voting and resetting votes entirely.

Where do the polls go?
----------------------
SimplePoll stores all of the polls in a JSON file, you can use something like jQuery to parse the JSON with
ease and then display the polls in any way you want.

How do I install it?
--------------------
No real installation is needed, all you really need to do is interface with the class file however you want.
As of now, the files included are pretty rough, but they act as controllers to pass your inputs to the class and feed back
out information.  Remember, the only real file you need is the SimplePoll class file, you can code all the controllers yourself.

What if I just want to use this?
--------------------------------
Not a problem, I have a pretty basic but also pretty sloppy setup already made for you.  If you look at the index.php file
in your browser, you'll see what I mean.

What if I want to just embed certain polls?
-------------------------------------------
That's not a problem either, you can keep the admin interface if you like, and then to embed a specific poll 
you can use the jQuery plugin designed to easily embed any poll you want with just a simple line

(not yet available)

Anything else?
--------------
Yes, I would like to say that my code is very sloppy and I'd like to clean it up and try to make the SimplePoll code
simple as well, minimizing anything I can and just have a really light PHP file that's easy to use but robust.
If you're interested, make sure to watch this project, as I will be sure to refine it over time.  And of course,
if anyone is willing to clone and push changes I'll be happy to accept anything that can improve this project.

