/**
 * This file uses the Page Object pattern to define the page object for tests
 * https://docs.google.com/presentation/d/1B6manhG0zEXkC-H-tPo2vwU06JhL8w9-XCF9oehXzAQ
 */

'use strict';

var ThePage = function() {
    this.menuButtons = element.all(by.css('.md-toolbar-tools a.nav-link'));
    this.hamburgerButton = element(by.css('.md-toolbar-tools button'));
    this.browseButton = element(by.css('.md-quote a'));
    this.mobileButtons = element.all(by.css('.md-sidenav-right a.md-button'));
    this.homeCards = element.all(by.css('.md-home-card h2'));
    this.footerLinks = element.all(by.css('footer a'));

    this.checkAlphaLinks = function() {
        var alphaLinks = element.all(by.repeater('letter in drugsList.letters'));
        expect(alphaLinks.getText()).toEqual([
            'A', 'B', 'C', 'D', 'E', 'F', 'G',
            'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P',
            'Q', 'R', 'S', 'T', 'U', 'V',
            'W', 'X', 'Y',
            'Z'
        ]);
    };

    this.checkAboutContent = function() {
        var content = element(by.css('.content-container'));
        expect(content.getText()).toContain('Request for Quotation (RFQ) 4QTFHS150004');
    };

    this.checkPrivacyContent = function() {
        var content = element(by.css('.privacy'));
        expect(content.getText()).toContain('operated by Symplicity Corporation');
        expect(content.getText()).toContain('required to comply with applicable laws');
        expect(content.getText()).toContain('user data may be included in aggregate reports');
        expect(content.getText()).toContain('Registration is not required');
        expect(content.getText()).toContain('Information you give to us must not contain sensitive data');
        expect(content.getText()).toContain('then you agree that it is at your own risk');
        expect(content.getText()).toContain('We collect personally identifiable information through the Site');
        expect(content.getText()).toContain('Symplicity may also disclose personally identifiable information');
        expect(content.getText()).toContain('our servers log your IP address');
        expect(content.getText()).toContain('If you are under 13, please do not use the Site');
        expect(content.getText()).toContain('personally identifiable information is restricted');
        expect(content.getText()).toContain('Symplicity does not review submissions');
        expect(content.getText()).toContain('Symplicity Corporation complies with the U.S.-EU Safe Harbor Framework');
    };
};

module.exports = new ThePage();
