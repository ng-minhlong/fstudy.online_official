import subprocess
import tempfile
import os
from flask import Flask, request, jsonify, Blueprint
import requests


complier_analyzer_bp = Blueprint('compliler-analyzer', __name__)



def run_python(code):
    with tempfile.NamedTemporaryFile('w', suffix='.py', delete=False) as f:
        f.write(code)
        path = f.name
    try:
        proc = subprocess.run(['python3', path], capture_output=True, text=True, timeout=10)
        return proc.returncode, proc.stdout, proc.stderr
    finally:
        os.unlink(path)

def run_cpp(code):
    with tempfile.TemporaryDirectory() as tmpdir:
        source_file = os.path.join(tmpdir, 'prog.cpp')
        exe_file = os.path.join(tmpdir, 'prog.exe')
        with open(source_file, 'w') as f:
            f.write(code)
        compile_proc = subprocess.run(['g++', source_file, '-o', exe_file], capture_output=True, text=True)
        if compile_proc.returncode != 0:
            return compile_proc.returncode, '', compile_proc.stderr
        run_proc = subprocess.run([exe_file], capture_output=True, text=True, timeout=10)
        return run_proc.returncode, run_proc.stdout, run_proc.stderr

def run_java(code):
    with tempfile.TemporaryDirectory() as tmpdir:
        source_file = os.path.join(tmpdir, 'Main.java')
        with open(source_file, 'w') as f:
            f.write(code)
        compile_proc = subprocess.run(['javac', source_file], capture_output=True, text=True)
        if compile_proc.returncode != 0:
            return compile_proc.returncode, '', compile_proc.stderr
        run_proc = subprocess.run(['java', '-cp', tmpdir, 'Main'], capture_output=True, text=True, timeout=10)
        return run_proc.returncode, run_proc.stdout, run_proc.stderr

@complier_analyzer_bp.route('/complier-check', methods=['POST'])

def checking():
    data = request.json
    code = data.get('code')
    lang = data.get('lang')
    problem_id = data.get('id')

    # Lấy test case
    try:
        resp = requests.post("http://localhost/fstudy/api/backend/get-test-case", json={"id_problem": problem_id}, timeout=5)
        resp.raise_for_status()
        json_data = resp.json()
    except Exception as e:
        return jsonify({'error': 'Failed to get test cases', 'detail': str(e)}), 500

    raw_test_code = json_data.get('data', {}).get('test_cases')
    if not raw_test_code:
        return jsonify({'error': 'Missing test cases'}), 400

    full_code = code + "\n\n" + raw_test_code

    # Run theo lang
    if lang == "Python":
        code, stdout, stderr = run_python(full_code)
    elif lang == "Cpp":
        code, stdout, stderr = run_cpp(full_code)
    elif lang == "Java":
        code, stdout, stderr = run_java(full_code)
    else:
        return jsonify({'error': f'Unsupported language: {lang}'}), 400

    if code == 0:
        return jsonify({'result': 'passed', 'output': stdout.strip()})
    else:
        return jsonify({'result': 'failed', 'output': stdout.strip(), 'error': stderr.strip()})




__all__ = ['complier_analyzer_bp']
