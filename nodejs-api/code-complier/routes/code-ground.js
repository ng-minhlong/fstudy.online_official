const express = require("express");
const compiler = require("../compilerInstance");

const router = express.Router();
router.post("/compile", (req, res) => {
    const { code, input, lang } = req.body;
    const envData = { OS: "windows", cmd: "g++", options: { timeout: 10000 } };

    try {
        if (lang === "Cpp") {
            const fn = input ? compiler.compileCPPWithInput : compiler.compileCPP;
            fn(envData, code, input, (data) => res.send(data.output ? data : { output: "error" }));
        } else if (lang === "Java") {
            const fn = input ? compiler.compileJavaWithInput : compiler.compileJava;
            fn(envData, code, input, (data) => res.send(data.output ? data : { output: "error" }));
        } else if (lang === "Python") {
            if (input) {
                compiler.compilePythonWithInput(envData, code, input, (data) => {
                    res.send(data.output ? data : { output: "error" });
                });
            } else {
                compiler.compilePython(envData, code, (data) => {
                    res.send(data.output ? data : { output: "error" });
                });
            }
        }
        else {
            res.status(400).send({ output: "Unsupported language" });
        }
    } catch (err) {
        console.log("Compile error:", err);
        res.status(500).send({ output: "error" });
    }
});

// Route flush temp files
router.delete("/delete-temp-files", (req, res) => {
    compiler.flush(() => {
        console.log("Temporary files deleted.");
        res.status(200).json({ message: "Temporary files deleted." });
    });
});

module.exports = router;