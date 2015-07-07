'use strict';

describe('The drugs view', function() {
    var page;

    beforeEach(function() {
        if (!page) {
            browser.manage().window().setSize(1024, 768);
            browser.get(browser.baseUrl);
            page = require('./main.po');
        }
    });

    it('should provide alphabetical navigation', function() {
        page.menuButtons.get(1).click();
        var alphaLinks = element.all(by.repeater('letter in drugsList.letters'));
        for (var i = 26; i >= 0; i--) {
            var link = alphaLinks.get(i);
            link.click();
            browser.waitForAngular();
            var currentLetter = element(by.css('.md-drugs h3'));
            expect(link.getText()).toEqual(currentLetter.getText());
        }
    });
});
