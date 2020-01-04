[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/AlexanderGranhof/ramverk1-projekt/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/AlexanderGranhof/ramverk1-projekt/?branch=master)
[![CircleCI](https://circleci.com/gh/AlexanderGranhof/ramverk1-projekt.svg?style=svg)](https://circleci.com/gh/AlexanderGranhof/ramverk1-projekt)

# ramverk1-projekt
This repo is a project in a course at Blekinge Tekniska Högskola.

# Installation
1. `clone the repo`
2. run `composer install`

# Development
To further develop this project, there are a few areas of intrests listed below.

### Style
To change the styling of the site, you need to navigate to `/theme`. Then run `npm install` followed by `npm run watch`. This will start a less watch complier and will compile the file `/theme/src/main.less` to `/htdocs/css`.

### Controllers
To modify the controllers and the data sent to all of the views, you can modify them at `/src/Controller`.

### Views
To modify the views and how the HTML structure is in the view. (Not including the footer and navbar, only in the body). You can navigate to `/view/algn` and modify the views there.

# Testing
To run unittests you can run `make test` to run all the unit tests. If you want to add or modify. Make sure the test does not have any database connections running. Since this will not work in circleCI. You can do this at `/test/NoDB`.
