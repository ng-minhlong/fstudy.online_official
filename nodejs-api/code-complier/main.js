const express = require("express");
const bodyParser = require("body-parser");
const cors = require("cors");

const app = express();
const codeGroundRoutes = require("./routes/code-ground");
const compileRoutes = require("./routes/submitPractice");
const compileTestCaseRoutes = require("./routes/submitTestCasePractice");

app.use(cors());
app.use(bodyParser.json());
app.use(express.json());
app.use("/codemirror-5.65.17", express.static("codemirror-5.65.17"));

app.use("/api", codeGroundRoutes);
app.use("/api", compileRoutes);
app.use("/api", compileTestCaseRoutes);

app.listen(8000, () => {
    console.log("Server running at http://localhost:8000");
});
