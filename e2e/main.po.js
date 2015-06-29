/**
 * This file uses the Page Object pattern to define the main page for tests
 * https://docs.google.com/presentation/d/1B6manhG0zEXkC-H-tPo2vwU06JhL8w9-XCF9oehXzAQ
 */

'use strict';

var MainPage = function() {
    this.menuButtons = element.all(by.css('.md-toolbar-tools a.nav-link span'));
    this.hamburgerButton = element(by.css('.md-toolbar-tools button'));
    this.mobileButtons = element.all(by.css('.md-sidenav-right a.md-button span'));
    this.homeCards = element.all(by.css('.md-home-card h2'));
};

module.exports = new MainPage();
