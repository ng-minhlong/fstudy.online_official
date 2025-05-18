function getOverallBand() {
    // Calculate averages and overall points
    let averageLexicalResourcePart1 = lexical_resource_all_point_part1 / part1Count;
    let averageLexicalResourcePart2 = lexical_resource_all_point_part2 / part2Count;
    let averageLexicalResourcePart3 = lexical_resource_all_point_part3 / part3Count;
  
    let averageFluencyAndCoherencePart1 = fluency_and_coherence_all_point_part1 / part1Count;
    let averageFluencyAndCoherencePart2 = fluency_and_coherence_all_point_part2 / part2Count;
    let averageFluencyAndCoherencePart3 = fluency_and_coherence_all_point_part3 / part3Count;
  
    let averageGrammaticalRangeAndAccuracyPart1 = grammatical_range_and_accuracy_all_point_part1 / part1Count;
    let averageGrammaticalRangeAndAccuracyPart2 = grammatical_range_and_accuracy_all_point_part2 / part2Count;
    let averageGrammaticalRangeAndAccuracyPart3 = grammatical_range_and_accuracy_all_point_part3 / part3Count;
  
    let averagePronunciationPart1 = pronunciation_all_point_part1 / part1Count;
    let averagePronunciationPart2 = pronunciation_all_point_part2 / part2Count;
    let averagePronunciationPart3 = pronunciation_all_point_part3 / part3Count;
  
    let overallLexicalResourcePoint =
      0.2 * averageLexicalResourcePart1 +
      0.4 * averageLexicalResourcePart2 +
      0.4 * averageLexicalResourcePart3;
    let overallFluencyAndCoherencePoint =
      0.2 * averageFluencyAndCoherencePart1 +
      0.4 * averageFluencyAndCoherencePart2 +
      0.4 * averageFluencyAndCoherencePart3;
    let overallGrammaticalRangeAndAccuracyPoint =
      0.2 * averageGrammaticalRangeAndAccuracyPart1 +
      0.4 * averageGrammaticalRangeAndAccuracyPart2 +
      0.4 * averageGrammaticalRangeAndAccuracyPart3;
    let overallPronunciationPoint =
      0.2 * averagePronunciationPart1 +
      0.4 * averagePronunciationPart2 +
      0.4 * averagePronunciationPart3;
  
    let overallBandFinal =
      (overallLexicalResourcePoint +
        overallFluencyAndCoherencePoint +
        overallGrammaticalRangeAndAccuracyPoint +
        overallPronunciationPoint) /
      4;
  

    let userResult =  document.getElementById('userResult');
    userResult.innerText = `${overallBandFinal.toFixed(2)}`;

    let userBandDetail = document.getElementById('userBandDetail');
  
    userBandDetail.innerHTML = `Lexical Resource: <p id = "final_lexical_resource_point">${overallLexicalResourcePoint}<p> <br> Fluency and Coherence: <p id = "final_fluency_and_coherence_point"> ${overallFluencyAndCoherencePoint} </p> <br> Grammatical Range and Accuracy:  <p id = "final_grammatical_range_and_accuracy_point">${overallGrammaticalRangeAndAccuracyPoint}</p> <br> Pronunciation: <p  id = "final_pronunciation_point">${overallPronunciationPoint}</p>`


    // Calculate percentages for pie charts
    let overallLexicalResourcePointPercentage =
      (overallLexicalResourcePoint / 9) * 100;
    let overallFluencyAndCoherencePointPercentage =
      (overallFluencyAndCoherencePoint / 9) * 100;
    let overallGrammaticalRangeAndAccuracyPointPercentage =
      (overallGrammaticalRangeAndAccuracyPoint / 9) * 100;
    let overallPronunciationPointPercentage =
      (overallPronunciationPoint / 9) * 100;
  
    // Get the element by ID
    let BreakDownElement = document.getElementById("breakdown");
  
    // Inject pie charts into the #pie-chart-final div
    let pieChartHTML = `
    ${createPieChart(overallLexicalResourcePointPercentage, 'lightgreen', 'Lexical Resource')}
    ${createPieChart(overallFluencyAndCoherencePointPercentage, 'lightblue', 'Fluency And Coherence')}
    ${createPieChart(overallGrammaticalRangeAndAccuracyPointPercentage, 'orange', 'Grammatical Range & Accuracy')}
    ${createPieChart(overallPronunciationPointPercentage, 'purple', 'Pronunciation')}
    `;
  
    // Inject breakdown details
    BreakDownElement.innerHTML = `
      <div id ="final-result-chart">
           <div id="pie-chart-final">${pieChartHTML}</div>
           <table>
               <h3 style="color:red">Breakdown</h3> 
               <tr>
                   <td>Lexical Resource: ${overallLexicalResourcePoint}</td>
                   <td>Fluency And Coherence: ${overallFluencyAndCoherencePoint}</td>
                   <td>Grammatical Range And Accuracy: ${overallGrammaticalRangeAndAccuracyPoint}</td>
                   <td>Pronunciation: ${overallPronunciationPoint}</td>
                </tr>
            </table> <br>   
            <div id = "overall-band-div">
              <h2 style="color:red">Overall Band: ${overallBandFinal.toFixed(2)}</h2> 
            </div>
      </div>
  
      <h4>Part 1: Overall General:</h4><br>
      <p style="font-style:italic"> Number of questions part 1: ${part1Count}</p><br>
      Full point Lexical Resource: ${lexical_resource_all_point_part1}. Average Point: ${averageLexicalResourcePart1} <br>
      Full point Fluency and Coherence: ${fluency_and_coherence_all_point_part1}. Average Point: ${averageFluencyAndCoherencePart1}<br>
      Full point Grammatical range and accuracy: ${grammatical_range_and_accuracy_all_point_part1}. Average Point: ${averageGrammaticalRangeAndAccuracyPart1}<br>
      Full point Pronunciation: ${pronunciation_all_point_part1}. Average Point: ${averagePronunciationPart1} <br>
  
      <h4>Part 2: Overall General:</h4><br>
      <p style="font-style:italic"> Number of questions part 2: ${part2Count}</p><br>
      Full point Lexical Resource Part 2: ${lexical_resource_all_point_part2}. Average Point: ${averageLexicalResourcePart2} <br>
      Full point Fluency and Coherence Part 2: ${fluency_and_coherence_all_point_part2}. Average Point: ${averageFluencyAndCoherencePart2}<br>
      Full point Grammatical range and accuracy: ${grammatical_range_and_accuracy_all_point_part2}. Average Point: ${averageGrammaticalRangeAndAccuracyPart2}<br>
      Full point Pronunciation: ${pronunciation_all_point_part2}. Average Point: ${averagePronunciationPart2} <br>
  
      <h4>Part 3: Overall General:</h4><br>
      <p style="font-style:italic"> Number of questions part 3: ${part3Count}</p><br>
      Full point Lexical Resource Part 3: ${lexical_resource_all_point_part3}. Average Point: ${averageLexicalResourcePart3} <br>
      Full point Fluency and Coherence Part 3: ${fluency_and_coherence_all_point_part3}. Average Point: ${averageFluencyAndCoherencePart3} <br>
      Full point Grammatical range and accuracy: ${grammatical_range_and_accuracy_all_point_part3}. Average Point: ${averageGrammaticalRangeAndAccuracyPart3} <br>
      Full point Pronunciation: ${pronunciation_all_point_part3}. Average Point: ${averagePronunciationPart3} <br>
  
      <p style="color:red">Developer Print Debug/ Missings</p>
      <p style="font-style:italic"> Number of unknown questions: ${unknownPartCount}.</p><br>
    `;
  }
  
  function createPieChart(percentage, color, label) {
    return `
        <div class="pie-container">
            <div class="pie animate" style="--p:${percentage};--c:${color}">
                ${percentage.toFixed(0)}%
            </div>
            <div class="pie-label">${label}</div>
        </div>`;
}
  