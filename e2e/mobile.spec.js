'use strict';

describe('The mobile view', function() {
    var page;

    beforeEach(function() {
        if (!page) {
            browser.manage().window().setSize(480, 640);
            browser.get(browser.baseUrl);
            page = require('./main.po');
        }
    });

    it('should should be responsive', function() {
        expect(page.menuButtons.getText()).toEqual(['', '', '', '', '']);
        expect(page.mobileButtons.getText()).toEqual(['', '', '', '', '']);
        expect(page.homeCards.getText()).toEqual(['Insurance', 'Personalized', 'Trusted']);
    });

    it('should provide navigation', function() {
        page.hamburgerButton.click();
        expect(page.mobileButtons.getText()).toEqual(['HOME', 'MEDICATIONS', 'ABOUT', 'SIGN UP', 'LOG IN']);

        page.mobileButtons.get(1).click();
        page.checkAlphaLinks();

        page.hamburgerButton.click();
        page.mobileButtons.get(2).click();
        page.checkAboutContent();
    });
});
