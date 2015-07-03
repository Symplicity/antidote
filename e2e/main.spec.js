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

    it('should provide browse button', function() {
        page.menuButtons.get(0).click();
        browser.waitForAngular();
        page.browseButton.click();
        page.checkAlphaLinks();
    });

    it('should provide footer links', function() {
        expect(page.footerLinks.getText()).toEqual([
            'Terms of Service', 'Privacy Policy', 'View Project on GitHub', 'API Documentation', 'Support'
        ]);

        page.footerLinks.get(0).click();
        page.checkLegalContent('Terms of Service', [
            'Legal Notices',
            'Permitted and Prohibited Uses',
            'User Submissions',
            'User Discussion Lists and Forums',
            'Use of Personally Identifiable Information',
            'Indemnification',
            'Termination',
            'WARRANTY DISCLAIMER',
            'General',
            'Links to Other Materials',
            'Notification of Possible Copyright Infringement'
        ]);

        page.footerLinks.get(1).click();
        page.checkLegalContent('Privacy Policy', [
            'Privacy Policy',
            'YOUR USE OF THE SITE',
            'SHARING OF DATA',
            'USE OF DATA',
            'USE OF PERSONALLY IDENTIFIABLE INFORMATION',
            'UPDATING ACCOUNT INFORMATION',
            'INTERNET PROTOCOL (IP)',
            'COOKIES',
            'CHILDREN',
            'SECURITY',
            'PRIVACY POLICY CHANGES',
            'CALIFORNIA RESIDENTS',
            'QUESTIONS OR CONCERNS',
            'SAFE HARBOR'
        ]);
    });
});
