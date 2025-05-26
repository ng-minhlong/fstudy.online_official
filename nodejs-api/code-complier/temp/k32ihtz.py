def get_row(rowIndex):
    row = [1] * (rowIndex + 1)
    for i in range(1, rowIndex + 1):
        for j in range(i - 1, 0, -1):
            row[j] += row[j - 1]
    return row

if __name__ == "__main__":
    s = input().strip()
    #s = list(map(int, input().split())) #cái này là nhập string -> tạo ra chuỗi như [1,2,3,...]
    numRows = int(input())
    result = convert(s, numRows)
    print(result)