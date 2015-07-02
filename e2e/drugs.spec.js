'use strict';

describe('The drugs view', function() {
    var page;

    beforeEach(function() {
        if (!page) {
            browser.manage().window().setSize(1024, 768);
            // start with B to make sure we test a click on A
            browser.get(browser.baseUrl + 'drugs/?term=b');
            page = require('./main.po');
        }
    });

    it('should provide alphabetical navigation', function() {
        var alphaLinks = element.all(by.repeater('letter in drugsList.letters'));
        for (var i = 0; i < 26; i++) {
            var link = alphaLinks.get(i);
            link.click();
            browser.waitForAngular();
            var currentLetter = element(by.css('.md-drugs h3'));
            expect(link.getText()).toEqual(currentLetter.getText());
        }
    });
});
