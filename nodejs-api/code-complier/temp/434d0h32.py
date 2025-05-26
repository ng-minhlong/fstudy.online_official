def restore_ip_addresses(s):
    def dfs(s, start, ip, result, part):
        if part == 4 and start == len(s):
            result.append(ip[:-1])
            return
        if part == 4 or start >= len(s):
            return

        num = 0
        for i in range(start, len(s)):
            num = num * 10 + int(s[i])
            if num > 255:
                break
            dfs(s, i + 1, ip + s[i] + ".", result, part + 1)
            if num == 0:
                break

    result = []
    dfs(s, 0, "", result, 0)
    return result

if __name__ == "__main__":
    s = input().strip()
    s = list(map(int, input().split())) #cái này là nhập string -> tạo ra chuỗi như [1,2,3,...]
    numRows = int(input())
    result = restore_ip_addresses(s, numRows)
    print(result)