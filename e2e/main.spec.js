'use strict';

describe('The main view', function() {
    var page;

    beforeEach(function() {
        if (!page) {
            browser.manage().window().setSize(1024, 768);
            browser.get(browser.baseUrl);
            page = require('./main.po');
        }
    });

    it('should identify the system', function() {
        expect(page.homeCards.getText()).toEqual(['Insurance', 'Personalized', 'Trusted']);
    });

    it('should provide navigation', function() {
        expect(page.menuButtons.getText()).toEqual(['HOME', 'MEDICATIONS', 'ABOUT', 'SIGN UP', 'LOG IN']);
        expect(page.mobileButtons.getText()).toEqual(['', '', '', '', '']);

        page.menuButtons.get(1).click();
        page.checkAlphaLinks();

        page.menuButtons.get(2).click();
        page.checkAboutContent();
    });

    it('should provide footer links', function() {
        expect(page.footerLinks.getText()).toEqual([
            'Terms of Service', 'Privacy Policy', 'View Project on GitHub', 'API Documentation', 'Support'
        ]);
    });
});
