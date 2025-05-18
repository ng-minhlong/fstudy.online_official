const express = require("express");
const fetch = (...args) => import("node-fetch").then(({ default: fetch }) => fetch(...args));
const compiler = require("../compilerInstance");

const router = express.Router();

function formatInput(input) {
  let result = [];

  if (typeof input !== 'object' || input === null) return "";

  for (const key in input) {
    const value = input[key];
    if (Array.isArray(value)) {
      result.push(value.join(" "));
    } else {
      result.push(String(value));
    }
  }

  return result.join("\n");
}


function normalizeOutput(str, preserveWhitespace = false) {
    if (preserveWhitespace) {
        // Only normalize line endings but preserve all other whitespace
        return str.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
    }
    // Original normalization for cases where whitespace doesn't matter
    return str
        .trim()
        .replace(/\r/g, '')
        .replace(/\n/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();
}



router.post("/compileTestCase", async (req, res) => {
    const { code, lang, id, SampletestCases } = req.body;
    const envData = { OS: "windows", cmd: "g++", options: { timeout: 10000 } };

    const startTime = Date.now();

    try {
        let testCases = SampletestCases; 

        if (typeof testCases === "string") {
            try {
                testCases = JSON.parse(testCases);
            } catch (e) {
                return res.status(400).json({ output: "Failed to parse test cases JSON." });
            }
        }

        if (!Array.isArray(testCases)) {
            return res.status(400).json({ output: "Invalid or missing test cases." });
        }

        const fn = {
            Cpp: compiler.compileCPPWithInput,
            Java: compiler.compileJavaWithInput,
            Python: compiler.compilePythonWithInput,
        }[lang];

        if (!fn) {
            return res.status(400).json({ output: "Unsupported language." });
        }

        const results = [];

        for (const test of testCases) {
            const inputData = formatInput(test.input);
            const testStartTime = Date.now();

            await new Promise((resolve) => {fn(envData, code, inputData, (data) => {
                const testFinishTime = `${Date.now() - testStartTime}ms`;

                if (!data.output) {
                    results.push({
                        test_case_input_detail: inputData,
                        test_case: test.test_case,
                        user_code_output: "error",
                        passed: false,
                        message: "No output from compiler",
                        task_finish_time: testFinishTime,
                    });
                    return resolve();
                }

                const rawOutput = data.output.trim();
                let expectedOutput = typeof test.expected_output === "string" 
                    ? test.expected_output 
                    : JSON.stringify(test.expected_output);
                
                // Xử lý trường hợp expectedOutput là mảng JSON
                if (expectedOutput.startsWith('[') && expectedOutput.endsWith(']')) {
                    try {
                        const expectedArray = JSON.parse(expectedOutput);
                        const normalizedActual = normalizeOutput(rawOutput);
                        
                        // Kiểm tra nếu output trùng với bất kỳ phần tử nào trong mảng
                        const passed = expectedArray.some(item => 
                            normalizedActual === normalizeOutput(item.toString())
                        );
                        
                        results.push({
                            test_case_input_detail: inputData,
                            test_case: test.test_case,
                            user_code_output: rawOutput,
                            passed,
                            expectedOutput: expectedOutput,
                            message: passed 
                                ? "OK" 
                                : `Expected one of ${expectedOutput}, got "${rawOutput}"`,
                            task_finish_time: testFinishTime,
                        });
                        
                        return resolve();
                    } catch (e) {
                        // Nếu parse JSON thất bại, xử lý như bình thường
                        console.error("Failed to parse expected output as JSON:", e);
                    }
                }

                // Xử lý trường hợp thông thường (không phải mảng)
                const normalizedExpected = normalizeOutput(expectedOutput);
                const normalizedActual = normalizeOutput(rawOutput);
                const passed = normalizedActual === normalizedExpected;

                results.push({
                    test_case_input_detail: inputData,
                    test_case: test.test_case,
                    user_code_output: rawOutput,
                    passed,
                    expectedOutput: expectedOutput,
                    message: passed 
                        ? "OK" 
                        : `Expected "${normalizedExpected}", got "${normalizedActual}"`,
                    task_finish_time: testFinishTime,
                });

                resolve();
            });
            });
        }

        const totalTime = `${Date.now() - startTime}ms`;
        const total_testcase = results.length;
        const total_correct = results.filter(r => r.passed).length;

        return res.status(200).json({
            results,
            total_testcase,
            total_correct,
            expected_memory: "N/A",
            task_finish_time: totalTime,
            language: lang
        });

    } catch (err) {
        console.error("Test error:", err);
        return res.status(500).json({ output: "Internal error." });
    }
});


router.delete("/delete-temp-files", (req, res) => {
    compiler.flush(() => {
        console.log("Temporary files deleted.");
        res.status(200).json({ message: "Temporary files deleted." });
    });
});

module.exports = router;
