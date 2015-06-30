'use strict';

describe('The main view', function() {
    var page;

    beforeEach(function() {
        browser.manage().window().setSize(1024, 768);
        browser.get(browser.baseUrl);
        page = require('./main.po');
    });

    it('should identify the system', function() {
        expect(page.menuButtons.getText()).toEqual(['HOME', 'MEDICATIONS', 'ABOUT', 'SIGN UP', 'LOG IN']);
        expect(page.mobileButtons.getText()).toEqual(['', '', '', '', '']);
        expect(page.homeCards.getText()).toEqual(['Insurance', 'Personalized', 'Trusted']);
    });
});

describe('The main mobile view', function() {
    var page;

    beforeEach(function() {
        browser.manage().window().setSize(480, 640);
        browser.get(browser.baseUrl);
        page = require('./main.po');
    });

    it('should should be responsive', function() {
        expect(page.menuButtons.getText()).toEqual(['', '', '', '', '']);
        expect(page.mobileButtons.getText()).toEqual(['', '', '', '', '']);
        expect(page.homeCards.getText()).toEqual(['Insurance', 'Personalized', 'Trusted']);
        page.hamburgerButton.click();
        expect(page.mobileButtons.getText()).toEqual(['HOME', 'MEDICATIONS', 'ABOUT', 'SIGN UP', 'LOG IN']);
    });
});
