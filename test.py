k = int(input())
n, a, b, c = map(int, input().split())
if k == 1:
    kq = max(0, a + b + c - 2 * n)
elif k == 2:
    kq = min(a, b, c)
print(kq)

