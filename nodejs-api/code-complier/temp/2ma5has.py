from collections import defaultdict

def groupStrings(strings):
    groups = defaultdict(list)

    for s in strings:
        key = tuple((ord(c) - ord(s[0])) % 26 for c in s)
        groups[key].append(s)

    return list(groups.values())


if __name__ == "__main__":
    strings = list(map(input().split())) #cái này là nhập string -> tạo ra chuỗi như [1,2,3,...]
    result = groupStrings(strings)
    print(result)