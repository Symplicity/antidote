Prototype link: https://antidote.symplicity-opensource.com

## Introduction
Antidote provides a straight-forward experience that couples FDA and NLM data with crowd sourced reviews to provide better insights into medication success. Specifically, Antidote utilizes the following openFDA APIs:
* [openFDA drug label](https://open.fda.gov/drug/label/)
* [openFDA drug adverse events](https://open.fda.gov/drug/event/)
* [openFDA drug enforcement reports](https://open.fda.gov/drug/enforcement/)

Data utilized: Side effects, indications, recalls, description and prescription type

Antidote also utilizes APIs from the National Library of Medicine:
* [RxNorm](http://mor.nlm.nih.gov/download/rxnav/PrescribableAPIs.html)
* [RxClass](http://mor.nlm.nih.gov/download/rxnav/RxClassAPIs.html)

Data utilized: Master list of drugs, label, generic label and drug forms, and some relation information for alternatives.

Further, our platform [provides an API](http://docs.antidote.apiary.io/#) that transforms FDA and NIH data on drug and adverse effects information and couples user reviews in a clean, easy-to-use format. By providing information that combines clinical data with a review of medication experience and insurance coverage into a single source, we address a user need for immediate, real-time information on medication including side effects and crowd sourced product reviews that supplements offline touch points such as doctors or pharmacists. Using qualitative questions, we screened users via survey, and followed up with interviews to understand our users’ experiences. This feedback served as a grounding focus for all paper and live prototyping and iterations (See design process and iterations links). 

## Symplicity Agile Methodology
In effort to continuously monitor our product and provide on-going improvement we use the Google Heart Framework. Under this method we are going to measure Happiness, Engagement and Task Success as the fundamental topics that are applicable to our product features (Please refer to Google Heart Framework readme for details)
Antidote uses the principles of [Google Material Design visual language](https://www.google.com/design/spec/material-design/introduction.html) to deliver a unified system of visual, motion and interaction design across digital touch points. We apply Material Design by using the Angular.js instance of Material design where pre-defined user interface assets are leverage to rapidly build interfaces to Material Design specs.

Page titles, and interaction confirmations help the user understand systemic activity, and page location, with a friendly, personable tone for site content. Using solution-driven keywords will help users understand our content and simultaneously improve our search engine optimization rankings. Understanding that our potential users could possess disabilities, we used best practices from [WAI-ARIA](http://www.w3.org/WAI/intro/aria.php) and Usability communities to make sure all interactions, and content are accessible and can be read via screen readers. Links to a support page and community support forum are prominently displayed for assistance. Our designers use the best practices from industry and also resource techniques, and documentation from http://www.usability.gov/

Our minimum viable product has been driven by user feedback gained through qualitative feedback such as surveys, interviews, and user testing throughout the conception, planning, implementation and verification of (our product design process)(docs/Design-Process.mediawiki). Close communication among the team and with our users is fostered by face-to-face interaction and colocation wherever possible and daily standups and augmented by tools such as GoToMeeting and group chat via Slack. By using the agile-based workflow in [Pivotal Tracker] (docs/attachments/PivotalTrackerReport.pdf) for feature development, the team is able to visualize all work required and be aware of progress, as well as track a feature from conception to development to quality assurance and delivery. Github is accessible to all team members to report issues, house code, facilitate code reviews and control versions.

Symplicity assigns a Product Manager to all agile projects that can work directly with a Government product owner. The Product Manager maintains direct lines of communication to Symplicity senior management and is not encumbered by unnecessary levels of corporate bureaucracy that exists in large corporations.

Symplicity structures highly qualified teams to ensure three principles are met:
1) Advanced knowledge of the specific agency ecosystem and stakeholders.
2) Many years of experience in high-traffic agile delivery services.
3) Beneficial subject matter expertise on the specific business purpose the team will be building for.

Teams are structured to provide a high success rate, low risk solution to the Government. This is accomplished through:
* A full stack user experience processes, involving stakeholders and end users early and often.
* Structuring 1 or 2-week sprints, based on project scope, to ensure continuous integration, user validation, and tracking of any schedule variance.
* Standing up surge support teams to handle backlog if stories within a sprint are not fully completed at no cost to the Government.
* Using open source frameworks, tools and development to lower cost to the Government and provide greater transparency.
* Creating and and updating documentation on every aspect of the project to ensure ongoing ease of management and a smooth transition out process. This includes end user, API, and back end documentation and behavioral driven development.

## Antidote Technical Details
Antidote uses MariaDB and Lumen PHP micro framework. Authentication is managed by JWT authentication. We deploy using docker containers, with separate containers for web servers, database, backend workers, and loggers. This set up could be used with any system such as Amazon Beanstalk, Heroku, etc. Static assets can be served over free Cloudflare CDN, via Amazon CloudFront, or any other CDN. Actual public prototype is deployed to Digital Ocean via Tutum.

We use Protractor e2e tests for user-facing functionality, PHPUnit for server-side unit tests, karma/jasmine for unit tests of client-side javascript code, and Dredd to test our API. All tests are run automatically on every push to master branch (and on every pull request) via Codeship. Test code coverage and code quality is analyzed by Code Climate, and SensioLabs Insight checks for security issues. If all tests pass, automatic deployment script is triggered to update the prototype. Load and performance tests are performed regularly via Locust (locust.io), an open source load testing tool based on python. We created script that simulated user behavior, clicking through the site, logging in, signing up, and various expected actions when browsing the site. Using Locust, we simulated various loads (i.e. x number of users active concurrently), and tweaked our server/web configurations to be optimized for maximum load possible.

Google’s open source utility [Skipfish](https://code.google.com/p/skipfish/) was used to perform a full scan of production site. Results were shared with the team and any vulnerabilities were removed from the code. Scans were run periodically during development to make sure new code didn’t have any security issues and as of the latest run indicate no vulnerabilities. Personal information collected was limited to email, which was encrypted using a strong AES encryptions.

System-level resource utilization metrics will be collected and tracked by Prometheus and various supported exporters in real time, including OS and container level memory/cpu/networking stats, load balancer usage, cluster group sizes and usage, among others. Prometheus is an open-source service monitoring system and time series database which provides central metric gathering, statistical analytics, data visualization, health dashboards, and alerting, with out of box support for various open-source exporters that run on hosts and containers and it provides metrics gathered by central server. It is deployed in Docker containers and is scalable.

System performance metrics such as response time, latency, throughput, error rates will be collected and tracked by Promethus, and the ELK stack, which provide deep analytics and correlation ability on metrics gathered. With ELK (ElasticSearch, LogStash, Kibana, open-source, self-hostable with as-a-service available as well), logs of all types are shipped using open protocols to ELK which provides detailed analytics, searching, and correlations.  Metrics will be collected by Prometheus, which supports realtime calculation of N-percentile performance. Alerts will be triggered based on realtime Prometheus metrics, and sent via Email/SMS (with support for other channels such as IRC/Slack/HipChat/PagerDuty). Performance metrics and log data will be correlated with Prometheus and ELK layers. Both Prometheus and ELK layers provide realtime dashboards with customizable view of metrics and log data, with drill-down and searchability. Public facing metrics will be generated and presented from the Stats/Logging layers. 

The code is open source and available on github with issue tracking. Antidote’s open source platform was built using open source tools. You can find a full discussion of these tools in [Deployment doc](docs/Deployment.md) in addition to the design resources already mentioned.

There is a community forum posted at: https://www.facebook.com/pages/Antidote/496420223855079

The open source license is available at https://github.com/Symplicity/antidote/blob/master/LICENSE
