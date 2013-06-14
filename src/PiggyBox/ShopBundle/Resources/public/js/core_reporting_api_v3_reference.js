// Copyright 2012 Google Inc. All Rights Reserved.

/* Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * @fileoverview Reference example for the Core Reporting API. This example
 * demonstrates how to access the important information from version 3 of
 * the Google Analytics Core Reporting API.
 * @author api.nickm@gmail.com (Nick Mihailovski)
 */

// Simple place to store all the results before printing to the user.
var output = [];
var id;
id = 'ga:64666463';

// Initialize the UI Dates.
document.getElementById('start-date').value = lastNDays(365);
document.getElementById('end-date').value = lastNDays(0);

var startDate = lastNDays(365);
var endDate = lastNDays(0);


/**
 * Executes a Core Reporting API query to retrieve the top 25 organic search
 * terms. Once complete, handleCoreReportingResults is executed. Note: A user
 * must have gone through the Google APIs authorization routine and the Google
 * Anaytics client library must be loaded before this function is called.
 */
function makeApiCall() {
  gapi.client.analytics.data.ga.get({
    'ids': id,
    'start-date': document.getElementById('start-date').value,
    'end-date': document.getElementById('end-date').value,
    'metrics': 'ga:pageviews',
  }).execute(pagesView);
  
  gapi.client.analytics.data.ga.get({
    'ids': id,
    'start-date': document.getElementById('start-date').value,
    'end-date': document.getElementById('end-date').value,
    'metrics': 'ga:visits',
  }).execute(visits);
  
  gapi.client.analytics.data.ga.get({
    'ids': id,
    'start-date': document.getElementById('start-date').value,
    'end-date': document.getElementById('end-date').value,
    'metrics': 'ga:newVisits',
  }).execute(newVisits);
  
  gapi.client.analytics.data.ga.get({
    'ids': id,
    'start-date': document.getElementById('start-date').value,
    'end-date': document.getElementById('end-date').value,
    'metrics': 'ga:avgTimeOnSite',
  }).execute(timeOnSite);
  
  gapi.client.analytics.data.ga.get({
    'ids': id,
    'start-date': document.getElementById('start-date').value,
    'end-date': document.getElementById('end-date').value,
    'metrics': 'ga:visits',
    'dimensions': 'ga:isMobile',
  }).execute(isMobile);
  
  
  gapi.client.analytics.data.ga.get({
    'ids': id,
    'start-date': document.getElementById('start-date').value,
    'end-date': document.getElementById('end-date').value,
    'metrics': 'ga:pageviews',
    'dimensions': 'ga:pagePath',
	'sort': '-ga:pageviews',
	'filters': 'ga:pagePath=@/'+ slug,
  }).execute(pageCommercant);
  
  gapi.client.analytics.data.ga.get({
    'ids': id,
    'start-date': document.getElementById('start-date').value,
    'end-date': document.getElementById('end-date').value,
    'metrics': 'ga:visits',
    'dimensions': 'ga:source',
	'sort': '-ga:visits',
  }).execute(source);
}


function pagesView(results) {
  if (!results.code) {
	viewResultsAnalytics.style.visibility = '';
	var div = document.getElementById('pagesViews');
    div.innerHTML = results.rows[0];
	outputToPage(output.join(''));
  } else {
    outputToPage('Erreur: ' + results.message);
  }
}

function visits(results) {
  if (!results.code) {
    var div = document.getElementById('visitsWebsite');
    div.innerHTML = results.rows[0];
  } else {
    outputToPage('Erreur: ' + results.message);
  }
}

function newVisits(results) {
  if (!results.code) {
	var div = document.getElementById('newVisits');
    div.innerHTML = results.rows[0];
  } else {
    outputToPage('Erreur: ' + results.message);
  }
}

function timeOnSite(results) {
  if (!results.code) {
	var div = document.getElementById('timeOnSite');
    var tm=new Date(results.rows[0]*1000) 
	var minutes=tm.getUTCMinutes(); 
	var seconds=tm.getUTCSeconds();
	if (seconds < 10){
		seconds = '0' + seconds;
		}
	div.innerHTML = minutes + ':' + seconds;
  } else {
    outputToPage('Erreur: ' + results.message);
  }
}

function isMobile(results) {
  if (!results.code) {
	var no= String(results.rows[0]);
	var yes = String(results.rows[1]);
	no = parseInt(no.substring(3));
	yes = parseInt(yes.substring(4));
	var percent = Math.round(yes*100/(yes+no));
	var desktop = 100-percent;
	
	var wrapper = new google.visualization.ChartWrapper({
		chartType: 'ColumnChart',
		dataTable: [['', 'Mobile', 'Ordinateur'],
					['', percent/100, desktop/100]],
		options: {'title': 'Utilisateurs sur mobile', vAxis: {format:'#%', minValue:0, maxValue:1}},
		containerId: 'mobile-chart'
	});
	wrapper.draw();

	
  } else {
    outputToPage('Erreur: ' + results.message);
  }
}

function pageCommercant(results) {
  if (!results.code) {
  var total = 0;
  var j = 0;
    //Pour le calcul du nombre de pages vues
    for (var i=0; i<results.rows.length; i++)
        {
        var value = String(results.rows[i]);
        var number = parseInt(value.indexOf(",")+1);
		var viewProduct = parseInt(value.substring(number));
		total += viewProduct;
            // Pour la recherche du slug des trois produits les plus vus
            if(j<3 && value.match('produits/[a-z]*/[a-z0-9.-]+'))
                {
                j +=1;
                var slugProduct= String(value.match('produits/[a-z]*/[a-z0-9.-]+'));
                var number1 = parseInt(slugProduct.indexOf("produits/")+1);
                slugProduct = slugProduct.substring(9);
                number1 = slugProduct.indexOf("/")+1;
                slugProduct = slugProduct.substring(number1);
				document.getElementById('maxViewArticle').innerHTML = '';
                var produit = loadProduct(slugProduct);
                //var div = document.getElementById('viewPerArticle');
                //div.innerHTML += ' Vue ' + viewProduct + ' fois';
                }
        }
var div = document.getElementById('pagesViewsShop');
div.innerHTML = total;
  } else {
    outputToPage('Erreur: ' + results.message);
  }
}


function source(results) {
  if (!results.code) {
  
  var totalSearch = 0;
  var totalDirect = 0;
  var totalAutre = 0;
  var total = 0;
  
	for (var i=0; i<results.rows.length; i++)
		{
		var current = results.rows[i];
		
		var value = String(current);
		var number = parseInt(value.indexOf(",")+1);
		var totalValue = parseInt(value.substring(number));
		total +=  totalValue;
		
		var testSearch = value.indexOf(".");
		var testDirect = value.indexOf("(direct)");
			
		if(testSearch == -1 && testDirect == -1)
			{
			totalSearch +=  totalValue;
			}
		
		if(testDirect != -1)
			{
			totalDirect += totalValue;
			}
			
		if(testSearch != -1 && testDirect == -1)
			{
			totalAutre += totalValue;
			}
		}
	
	var percentSearch = Math.round((100*totalSearch)/total);
	var percentDirect = Math.round((100*totalDirect)/total);
	var percentAutre = Math.round((100*totalAutre)/total);
	
	var data = google.visualization.arrayToDataTable([
    ['Source', 'Pourcentage'],
    ['Acces direct', percentDirect],
    ['Trafic de recherche', percentSearch],
	['Site Référents', percentAutre],
  ]);

  // Create and draw the visualization.
	var wrapper = new google.visualization.PieChart(document.getElementById('source-chart'));
	wrapper.draw(data, {title:"Visites du site"});

  } else {
    outputToPage('Erreur: ' + results.message);
  }
}

  
/**
 * Handles the response from the CVore Reporting API. If sucessful, the
 * results object from the API is passed to various printing functions.
 * If Erreur, a message with the error is printed to the user.
 * @param {Object} results The object returned from the API.
 */
function handleCoreReportingResults(results) {
  if (!results.code) {
    updatePage('Query Success');
    printRows(results);
    outputToPage(output.join(''));
  } else {
    outputToPage('Erreur: ' + results.message);
  }
}

/**
 * Prints all the column headers and rows of data as an HTML table.
 * @param {Object} results The object returned from the API.
 */
function printRows(results) {


  if (results.rows && results.rows.length) {
    var table = ['<table>'];

   // Put headers in table.
    table.push('<tr>');
    for (var i = 0, header; header = results.columnHeaders[i]; ++i) {
      table.push('<th>', header.name, '</th>');
    }
    table.push('</tr>');

    // Put cells in table.
    for (var i = 0, row; row = results.rows[i]; ++i) {
      table.push('<tr><td>', row.join('</td><td>'), '</td></tr>');
	  
    }
    table.push('</table>');

    output.push(table.join(''));
  } else {
    output.push('<p>No rows found.</p>');
  }
}


/**
 * Utility method to update the output section of the HTML page. Used
 * to output messages to the user. This overwrites any existing content
 * in the output area.
 * @param {String} output The HTML string to output.
 */
function outputToPage(output) {
  document.getElementById('output').innerHTML = output;
}

/**
 * Utility method to update the output section of the HTML page. Used
 * to output messages to the user. This appends content to any existing
 * content in the output area.
 * @param {String} output The HTML string to output.
 */
function updatePage(output) {
  document.getElementById('output').innerHTML += '';
}


/**
 * Utility method to return the lastNdays from today in the format yyyy-MM-dd.
 * @param {Number} n The number of days in the past from tpday that we should
 *     return a date. Value of 0 returns today.
 */
function lastNDays(n) {
  var today = new Date();
  var before = new Date();
  before.setDate(today.getDate() - n);

  var year = before.getFullYear();

  var month = before.getMonth() + 1;
  if (month < 10) {
    month = '0' + month;
  }

  var day = before.getDate();
  if (day < 10) {
    day = '0' + day;
  }

  return [year, month, day].join('-');
}
