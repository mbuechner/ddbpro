# Bootstrap subtheme

The DDBpro theme ``bootstrap_ddbpro`` is a subtheme of Drupals [Bootstrap theme](https://www.drupal.org/project/bootstrap). More information about sub-theming can be found [here](https://drupal.org/node/1978010).

### SASS
The Drupal Bootstrap subtheme ``bootstrap_ddbpro`` is using [Sass (stylesheet language)](https://en.wikipedia.org/wiki/Sass_%28stylesheet_language%29). Compiling a minified CSS file for the theme can be done following these steps:

#### Prerequisites
Make sure you have [Ruby](https://www.ruby-lang.org/de/) and [NodeJS](https://nodejs.org/) installed, so the following command will work.

 - ``node -v``
 - ``ruby -v``

#### Install Grunt
Next step is to install [Grunt](http://gruntjs.com/), a JavaScript Task Runner.

 - ``npm install -g grunt-cli``

#### Compass and Grunt plug-ins
Next step is to install the Compass and SASS ruby gems:

 - ``gem install compass``
 - ``gem install sass``

#### Grunt plugins
Next step is to install Grunt plugins SASS, Watch and Compass via node package manager:

 - ``npm install grunt-contrib-sass -save-dev``
 - ``npm install grunt-contrib-compass -save-dev``
 - ``npm install grunt-contrib-watch -save-dev``

#### Grunt dependencies
Next step is to let Grunt install all necessary dependencies. To do so please go to the directories ``themes\bootstrap_ddbpro\`` and run:

 - ``npm install``

#### Run Grunt
We're done. Now you can run Grunt and it'll watch for changes of the file ``themes\bootstrap_ddbpro\sass\bootstrap_ddbpro.min.scss``. Grunt will compile a minified CSS file under ``themes\bootstrap_ddbpro\css\bootstrap_ddbpro.min.css``.

 - ``grunt``
