from collections import defaultdict

def groupStrings(strings):
    groups = defaultdict(list)
    for s in strings:
        key = tuple((ord(c) - ord(s[0])) % 26 for c in s)
        groups[key].append(s)
    return list(groups.values())

if __name__ == "__main__":
    # Nhập: abc bcd acef xyz az ba a z
    strings = input("Enter strings: ").split()
    result = groupStrings(strings)
    print(result)
