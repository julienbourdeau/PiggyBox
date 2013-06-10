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
document.getElementById('start-date').value = lastNDays(14);
document.getElementById('end-date').value = lastNDays(0);


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
  }).execute(handleCoreReportingResults);
  
  gapi.client.analytics.data.ga.get({
    'ids': id,
    'start-date': document.getElementById('start-date').value,
    'end-date': document.getElementById('end-date').value,
    'metrics': 'ga:visits',
  }).execute(handleCoreReportingResults);
  
  gapi.client.analytics.data.ga.get({
    'ids': id,
    'start-date': document.getElementById('start-date').value,
    'end-date': document.getElementById('end-date').value,
    'metrics': 'ga:newVisits',
  }).execute(handleCoreReportingResults);
  
  gapi.client.analytics.data.ga.get({
    'ids': id,
    'start-date': document.getElementById('start-date').value,
    'end-date': document.getElementById('end-date').value,
    'metrics': 'ga:avgTimeOnSite',
  }).execute(handleCoreReportingResults);
  
  gapi.client.analytics.data.ga.get({
    'ids': id,
    'start-date': document.getElementById('start-date').value,
    'end-date': document.getElementById('end-date').value,
    'metrics': 'ga:visits',
    'dimensions': 'ga:isMobile',
  }).execute(handleCoreReportingResults);
  
  
  gapi.client.analytics.data.ga.get({
    'ids': id,
    'start-date': document.getElementById('start-date').value,
    'end-date': document.getElementById('end-date').value,
    'metrics': 'ga:pageviews',
    'dimensions': 'ga:pagePath',
	'filters': 'ga:pagePath=@/boucherie-zola',
  }).execute(handleCoreReportingResults);
}


/**
 * Handles the response from the CVore Reporting API. If sucessful, the
 * results object from the API is passed to various printing functions.
 * If there was an error, a message with the error is printed to the user.
 * @param {Object} results The object returned from the API.
 */
function handleCoreReportingResults(results) {
  if (!results.code) {
    outputToPage('Query Success');
    printRows(results);
    outputToPage(output.join(''));
  } else {
    outputToPage('There was an error: ' + results.message);
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
  document.getElementById('output').innerHTML += '<br>' + output;
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