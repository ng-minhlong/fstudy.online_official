class ListNode:
    def __init__(self, val=0, next=None):
        self.val = val
        self.next = next

def addTwoNumbers(l1: ListNode, l2: ListNode) -> ListNode:
    dummy = ListNode()
    current = dummy
    carry = 0
    
    while l1 or l2 or carry:
        val1 = l1.val if l1 else 0
        val2 = l2.val if l2 else 0
        
        total = val1 + val2 + carry
        carry = total // 10
        digit = total % 10
        
        current.next = ListNode(digit)
        current = current.next
        
        l1 = l1.next if l1 else None
        l2 = l2.next if l2 else None
    
    return dummy.next

def createLinkedList(lst):
    dummy = ListNode()
    current = dummy
    for num in lst:
        current.next = ListNode(num)
        current = current.next
    return dummy.next

def linkedListToList(node):
    result = []
    while node:
        result.append(node.val)
        node = node.next
    return result

def main():
    import sys
    input_lines = sys.stdin.read().splitlines()
    
    # Parse input
    l1 = list(map(int, input_lines[0].split()))
    l2 = list(map(int, input_lines[1].split())) if len(input_lines) > 1 else []
    
    linked_l1 = createLinkedList(l1)
    linked_l2 = createLinkedList(l2)
    
    result = addTwoNumbers(linked_l1, linked_l2)
    output = linkedListToList(result)
    
    # Format output without spaces
    print("[" + ",".join(map(str, output)) + "]")

if __name__ == "__main__":
    main()