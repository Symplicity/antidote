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

    this.checkLegalContent = function(title, subTitles) {
        var titleHeader = element(by.css('.header h2'));
        expect(titleHeader.getText()).toContain(title);

        var subHeaders = element.all(by.css('.legal h4'));
        expect(subHeaders.getText()).toEqual(subTitles);
    };
};

module.exports = new ThePage();
