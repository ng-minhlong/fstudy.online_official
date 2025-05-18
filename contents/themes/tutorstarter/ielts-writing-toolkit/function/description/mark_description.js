

// description for overall band
function MarkDescription(){
        
        if (overallband >= 0 && overallband < 1 ){
            userLevel="Did not attempt the test ";
            band_description = "You did not answer the questions.";
        }
        if (overallband >= 1 && overallband < 2 ){
            userLevel="Non-user  ";
            band_description = "You have no ability to use the language except a few isolated words.";
        }
        if (overallband >= 2 && overallband < 3 ){
            userLevel="Intermittent user";
            band_description = "You have great difficulty understanding spoken and written English.";
        }
        if (overallband >= 3 && overallband < 4 ){
            userLevel="Extremely limited user    ";
            band_description = "You convey and understand only general meaning in very familiar situations. There are frequent breakdowns in communication.";
        }
        if (overallband >= 4 && overallband < 5 ){
            userLevel="Limited user  ";
            band_description = "Your basic competence is limited to familiar situations. You frequently show problems in understanding and expression. You are not able to use complex language.";
        }
        if (overallband >= 5 && overallband < 6){
            userLevel="Modest user ";
            band_description = "You have a partial command of the language, and cope with overall meaning in most situations, although you are likely to make many mistakes. You should be able to handle basic communication in your own field.";}
        if (overallband >= 6 && overallband < 7 ){
            userLevel="Competent user";
            band_description = "Generally you have an effective command of the language despite some inaccuracies, inappropriate usage and misunderstandings. You can use and understand fairly complex language, particularly in familiar situations.";
        }
        if (overallband >= 7 && overallband < 8){
            userLevel="Good user ";
            band_description = "You have an operational command of the language, though with occasional inaccuracies, inappropriate usage and misunderstandings in some situations. Generally you handle complex language well and understand detailed reasoning.";
        }
        if (overallband >= 8 && overallband < 9 ){
            userLevel="Very good user   ";
            band_description = "You have a fully operational command of the language with only occasional unsystematic inaccuracies and inappropriate usage. You may misunderstand some things in unfamiliar situations. You handle complex detailed argumentation well.";
        }
        if (overallband == 9 ){
            userLevel="Expert user ";
            band_description = "You have a full operational command of the language. Your use of English is appropriate, accurate and fluent, and you show complete understanding.";
        }



        if(task_achievement_part_1 == 0){
            task_achievement_comment= `• does not attend<br>• does not attempt the task in any way <br>• writes a totally memorised response`
        }
        else if(task_achievement_part_1 >= 1 && task_achievement_part_1 < 2){
            task_achievement_comment = `answer is completely unrelated to the task `
        }
        else if(task_achievement_part_1 >= 2 && task_achievement_part_1 < 3){
            task_achievement_comment = `answer is barely related to the task `
        }
        else if(task_achievement_part_1 >= 3&& task_achievement_part_1 <4){
            task_achievement_comment = `• fails to address the task, which may have been completely misunderstood <br> • presents limited ideas which may be largely irrelevant/repetitive`
        }
        else if(task_achievement_part_1 >= 4 && task_achievement_part_1 < 5){
            task_achievement_comment = `• attempts to address the task but does not cover all key features/bullet points; the format may be inappropriate <br> • (GT) fails to clearly explain the purpose of the letter; the tone may be inappropriate <br> • may confuse key features/bullet points with detail; parts may be unclear, irrelevant, repetitive or inaccurate`
        }
        else if(task_achievement_part_1 >= 5 && task_achievement_part_1 < 6){
            task_achievement_comment = `• generally addresses the task; the format may be inappropriate in places <br>• (A) recounts detail mechanically with no clear overview; there may be no data to support the description <br> • (GT) may present a purpose for the letter that is unclear at times; the tone may be variable and sometimes inappropriate <br>• presents, but inadequately covers, key features/ bullet points; there may be a tendency to focus on details`
        }
        else if(task_achievement_part_1 >= 6 && task_achievement_part_1 < 7){
            task_achievement_comment = `• addresses the requirements of the task <br> • (A) presents an overview with information appropriately selected <br>• (GT) presents a purpose that is generally clear; there may be inconsistencies in tone <br>• presents and adequately highlights key features/ bullet points but details may be irrelevant, inappropriate or inaccurate`
        }
        else if(task_achievement_part_1 >= 7 && task_achievement_part_1 < 8){
            task_achievement_comment = `• covers the requirements of the task <br>• (A) presents a clear overview of main trends, differences or stages <br>• (GT) presents a clear purpose, with the tone consistent and appropriate<br> • clearly presents and highlights key features/bullet points but could be more fully extended`
        }
        else if(task_achievement_part_1 >= 8 && task_achievement_part_1 < 9){
            task_achievement_comment = ` • covers all requirements of the task sufficiently<br>• presents, highlights and illustrates key features/ bullet points clearly and appropriately`
        }
        else if(task_achievement_part_1 == 9){
            task_achievement_comment = ` • fully satisfies all the requirements of the task<br> • clearly presents a fully developed response`
        }

        if(coherence_and_cohesion_part_1 == 0){
            coherence_and_cohesion_comment = `fails to communicate any message `;
        }
        else if(coherence_and_cohesion_part_1 >= 1 && coherence_and_cohesion_part_1 < 2){
            coherence_and_cohesion_comment = `fails to communicate any message `;
        }
        else if(coherence_and_cohesion_part_1 >= 2 && coherence_and_cohesion_part_1 <3){
            coherence_and_cohesion_comment = `has very little control of organisational features`;
        }
        else if(coherence_and_cohesion_part_1 >= 3 && coherence_and_cohesion_part_1 < 4){
            coherence_and_cohesion_comment = `• does not organise ideas logically<br> • may use a very limited range of cohesive devices, and those used may not indicate a logical relationship between ideas`;
        }
        else if(coherence_and_cohesion_part_1 >= 4 && coherence_and_cohesion_part_1 < 5){
            coherence_and_cohesion_comment = `• presents information and ideas but these are not arranged coherently and there is no clear progression in the response <br>• uses some basic cohesive devices but these may be inaccurate or repetitive`;
        }
        else if(coherence_and_cohesion_part_1 >= 5 && coherence_and_cohesion_part_1 < 6){
            coherence_and_cohesion_comment = `• presents information with some organisation but there may be a lack of overall progression <br>• makes inadequate, inaccurate or over-use of cohesive devices<br>• may be repetitive because of lack of referencing and substitution`;
        }
        else if(coherence_and_cohesion_part_1 >= 6 && coherence_and_cohesion_part_1 < 7){
            coherence_and_cohesion_comment = `• arranges information and ideas coherently and there is a clear overall progression<br> • uses cohesive devices effectively, but cohesion within and/or between sentences may be faulty or mechanical<br> • may not always use referencing clearly or appropriately`;
        }
        else if(coherence_and_cohesion_part_1 >= 7 && coherence_and_cohesion_part_1 < 8){
            coherence_and_cohesion_comment = `• logically organises information and ideas; there is clear progression throughout<br>• uses a range of cohesive devices appropriately although there may be some under-/over-use`;
        }
        else if(coherence_and_cohesion_part_1 >= 8 && coherence_and_cohesion_part_1 < 9){
            coherence_and_cohesion_comment = `• sequences information and ideas logically <br>• manages all aspects of cohesion well<br>• uses paragraphing sufficiently and appropriately`;
        }
        else if(coherence_and_cohesion_part_1 == 9){
            coherence_and_cohesion_comment = `• uses cohesion in such a way that it attracts no attention<br>• skilfully manages paragraphing`;
        }


        if(lexical_resource_part_1 == 1){
            lexical_resource_comment =`• can only use a few isolated words`;
        }
        else if(lexical_resource_part_1 >= 2 && lexical_resource_part_1 < 3){
            lexical_resource_comment =`• uses an extremely limited range of vocabulary; essentially no control of word formation and/or spelling`;
        }
        else if(lexical_resource_part_1 >= 3 && lexical_resource_part_1 < 4){
            lexical_resource_comment =`• uses only a very limited range of words and expressions with very limited control of word formation and/or spelling <br>• errors may severely distort the message`;
        }
        else if(lexical_resource_part_1 >= 4 && lexical_resource_part_1 < 5){
            lexical_resource_comment =`• uses only basic vocabulary which may be used repetitively or which may be inappropriate for the task <br>• has limited control of word formation and/or spelling;<br>• errors may cause strain for the reader`;
        }
        else if(lexical_resource_part_1 >= 5 && lexical_resource_part_1 < 6){
            lexical_resource_comment =`• uses a limited range of vocabulary, but this is minimally adequate for the task <br>• may make noticeable errors in spelling and/or word formation that may cause some difficulty for the reader`;
        }
        else if(lexical_resource_part_1 >= 6 && lexical_resource_part_1 < 7){
            lexical_resource_comment =`• uses an adequate range of vocabulary for the task <br>• attempts to use less common vocabulary but with some inaccuracy <br>• makes some errors in spelling and/or word formation, but they do not impede communication`;
        }
        else if(lexical_resource_part_1 >= 7 && lexical_resource_part_1 < 8){
            lexical_resource_comment =`• uses a sufficient range of vocabulary to allow some flexibility and precision<br> • uses less common lexical items with some awareness of style and collocation <br>• may produce occasional errors in word choice, spelling and/or word formation`;
        }
        else if(lexical_resource_part_1 >= 8 && lexical_resource_part_1 < 9){
            lexical_resource_comment =`uses a wide range of vocabulary fluently and flexibly to convey precise meanings<br> • skilfully uses uncommon lexical items but there may be occasional inaccuracies in word choice and collocation<br> • produces rare errors in spelling and/or word formation`;
        }
        else if(lexical_resource_part_1 == 9){
            lexical_resource_comment =` uses a wide range of vocabulary with very natural and sophisticated control of lexical features; rare minor errors occur only as slips`;
        }

        if(grammatical_range_and_accuracy_part_1 == 1){
            grammatical_range_and_accuracy_comment =`• cannot use sentence forms at all`;
        }
        else if(grammatical_range_and_accuracy_part_1 >= 2 && grammatical_range_and_accuracy_part_1 < 3){
            grammatical_range_and_accuracy_comment =`• cannot use sentence forms except in memorised phrases`;
        }
        else if(grammatical_range_and_accuracy_part_1 >= 3 && grammatical_range_and_accuracy_part_1 <  4){
            grammatical_range_and_accuracy_comment =`• attempts sentence forms but errors in grammar and punctuation predominate and distort the meaning`;
        }
        else if(grammatical_range_and_accuracy_part_1 >= 4 && grammatical_range_and_accuracy_part_1 <  5){
            grammatical_range_and_accuracy_comment =`• uses only a very limited range of structures with only rare use of subordinate clauses<br>• some structures are accurate but errors predominate, and punctuation is often faulty`;
        }
        else if(grammatical_range_and_accuracy_part_1 >= 5 && grammatical_range_and_accuracy_part_1 < 6){
            grammatical_range_and_accuracy_comment =`• uses only a limited range of structures <br>• attempts complex sentences but these tend to be less accurate than simple sentences <br>• may make frequent grammatical errors and punctuation may be faulty; errors can cause some difficulty for the reader`;
        }
        else if(grammatical_range_and_accuracy_part_1 >= 6 && grammatical_range_and_accuracy_part_1 < 7){
            grammatical_range_and_accuracy_comment =`• uses a mix of simple and complex sentence forms<br>• makes some errors in grammar and punctuation but they rarely reduce communication`;
        }
        else if(grammatical_range_and_accuracy_part_1 >= 7 && grammatical_range_and_accuracy_part_1 < 8){
            grammatical_range_and_accuracy_comment =`• uses a variety of complex structures<br>• produces frequent error-free sentences<br>• has good control of grammar and punctuation but may make a few errors`;
        }
        else if(grammatical_range_and_accuracy_part_1 >= 8 && grammatical_range_and_accuracy_part_1 < 9){
            grammatical_range_and_accuracy_comment =`• uses a wide range of structures<br>• the majority of sentences are error-free<br>• makes only very occasional errors or inappropriacies`;
        }
        else if(grammatical_range_and_accuracy_part_1 == 9){
            grammatical_range_and_accuracy_comment =`• uses a wide range of structures with full flexibility and accuracy; rare minor errors occur only as slip’`;
        }
        

        

        }