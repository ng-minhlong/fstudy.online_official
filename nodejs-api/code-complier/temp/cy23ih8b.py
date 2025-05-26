def get_row(rowIndex):
    row = [1] * (rowIndex + 1)
    for i in range(1, rowIndex + 1):
        for j in range(i - 1, 0, -1):
            row[j] += row[j - 1]
    return row

if __name__ == "__main__":
    s = input().strip()
    result = get_row(s)
    print(result)