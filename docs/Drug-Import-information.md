### Info
The data in antidote comes from the [openFDA](https://open.fda.gov/api/reference/) and [RxNorm](http://rxnav.nlm.nih.gov/APIsOverview.html) APIs (the [Prescribable content](http://www.nlm.nih.gov/research/umls/rxnorm/docs/prescribe.html) that has an open licence). The import is done through an artisan command that is scheduled to run monthly 

`php artisan import:drug`

the command has several options that allow it to be used for debugging purposes. The options can be seen by running 

`php artisan help import:drug`

`  --limit - to limit the number of drugs imported`  
`  --skip - number of records to be skipped from the beginning`  
`  --ids - provide a csv list of rxcuis to be imported`  
`  --names - provide a csv list of drug names to be imported`  
`  --debug - do not save in the db, only print in console`  
`  --verbose - show the calls to the APIs`  

It utilizes the `OpenFDA`, `RXNorm` and `RXClass` services found in `app\Services` to make the actual API calls. The data between openfda and rxnorm is related through rxcuis (rxnorm concept unique identifiers)  ([more in RXNorm Technical Documentation](http://www.nlm.nih.gov/research/umls/rxnorm/docs/index.html)). The semantically normalized drug form rxcuis are linked to the openfda.rxcui field in the OpenFDA data through a process of harmonization ([more on the openfda field](https://open.fda.gov/api/reference/#openfda-fields)).

 The logic is the following:
  * get a list of brand rxcuis (brand drugs TTY:BN and brand packs TTY:BPCK) (either from the command line options or by calling RXNorm api) `ImportDrugs::getBrands`
  * for each brand get all related properties [TTYs] in the RXNorm database: Label, Drug Forms, Normalized Drugs, Ingredient list etc) `RXNorm::getConceptProperties` and `RXNorm::getConceptRelations`
  * use the related semantic brand rxcuis to find a label in the Openfda labels api and get the description form it `OpenFDA::getLabel`
     - the first available label that has an openfda.brand_name that matches the brand name form RXNorm
     - if such a label is not found find one that has a matching openfda.generic_name or openfda.substance_name
     - go through all associated Label records to find if the drug has an OTC (Over The Counter) or Prescription type (or both) (openfda.product_type field)
  * use both brand and generic semantic drug rxcuis to find recall information from the Openfda Enforcement records `OpenFDA::getRecalls`
  * use both brand and generic semantic drug rxcuis to find indications `OpenFDA::getIndications` and side effects `OpenFDA::getSideEffects` from the Openfda Adverse Events records.
    - indications are retrieved from counts in the patient.drug.drugindication field
    - side effects are retrieved from counts of the patient.reaction.reactionmeddrapt field
  * get related brand rxcuis (based on ingredient list) from rxnorm `RXNorm::getRelatedConcepts`. If there are not enough concepts that are related (currently 5) find related concepts based on ATC categories from rxclass `RXClass::getRelatedConcepts`. This information is used for the alternatives page.

###Notes: 
>  * The script will take about two hours to import all drug information due to rate limitations for querying the FDA and RXNorm data (currently 4 and 20 per second respectively). The rates can be changed by using OPENFDA_RATE_LIMIT, RXNORM_RATE_LIMIT and RXCLASS_RATE_LIMIT environment variables
>  * Make sure you sign up for an [openFDA api key](https://open.fda.gov/api/reference/#your-api-key) and add that to your environment variables in .env otherwise you might hit a [limit](https://open.fda.gov/api/reference/#authentication) on the per day requests
>  * Currently the count information from the adverse events APIs is not used but an improvement should be added to use this information to sort the side effects and indications list for the drug by importance (currently they are sorted alphabetically). 

### Debug:
to use the script to get information but not update the database use the --debug option.

Example:

`php artisan import:drugs --names="advil,excedrin pm" --debug --verbose`

Calls executed to the APIs:

`[RXNorm]: http://rxnav.nlm.nih.gov/REST/Prescribe/approximateTerm.json?term=advil&maxEntries=1`
`[RXNorm]: http://rxnav.nlm.nih.gov/REST/Prescribe/rxcui/153010/properties.json`
`[RXNorm]: http://rxnav.nlm.nih.gov/REST/Prescribe/approximateTerm.json?term=excedrin+pm&maxEntries=1`
`[RXNorm]: http://rxnav.nlm.nih.gov/REST/Prescribe/rxcui/217025/properties.json`
`[RXNorm]: http://rxnav.nlm.nih.gov/REST/Prescribe/rxcui/153010/properties.json`
`[RXNorm]: http://rxnav.nlm.nih.gov/REST/Prescribe/rxcui/153010/allrelated.json`
`[OpenFDA]: https://api.fda.gov/drug/label.json?search=openfda.rxcui(153008+206878+731531+731533+731535+731536+731529)&skip=0&limit=100`
`[OpenFDA]: https://api.fda.gov/drug/enforcement.json?search=openfda.rxcui(153008+206878+731531+731533+731535+731536+731529+204442+310965+197803+198405+310963+310964+314047)+AND+status:Ongoing&skip=0&limit=100`
`[OpenFDA]: https://api.fda.gov/drug/event.json?search=patient.drug.openfda.rxcui(153008+206878+731531+731533+731535+731536+731529+204442+310965+197803+198405+310963+310964+314047)&count=patient.drug.drugindication.exact`
`[OpenFDA]: https://api.fda.gov/drug/event.json?search=patient.drug.openfda.rxcui:153008+206878+731531+731533+731535+731536+731529+204442+310965+197803+198405+310963+310964+314047)&count=patient.reaction.reactionmeddrapt.exact`
`[RXNorm]: http://rxnav.nlm.nih.gov/REST/Prescribe/rxcui/5640/related.json?tty=BN+BPCK+MIN`

Output:

`==============================================================================`  
`Importing rxcui: [153010] as drug: [51] label: [Advil (Ibuprofen)]`  
`==============================================================================`  

`  rxcui                : 153010`  
`  label                : Advil`  
`  generic              : Ibuprofen`  
`  description          : temporarily relieves minor aches and pains due to: headache toothache backache menstrual cramps the common cold muscular aches minor pain of arthritis temporarily reduces fever`  
`  id                   : 51`  
`  effectiveness_percen : 0`  
`  insurance_coverage_p : 0`  
`  total_reviews        : 0`  
`  drug_forms           : [Chewable Tablet, Oral Capsule, Oral Suspension, Oral Tablet]`  
`  types                : [2]`  
`  indications          : [51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70]`  
`  side_effects         : [51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70]`  
`  related              : [153010, 165786, 202488, 1372755, 1542983, 215041, 217693, 217324, 219128, 220826, 284787, 578410, 637194, 643099, 702198, 854184, 579458, 643061, 850404, 895656]`  
`  recalls              : []`  


